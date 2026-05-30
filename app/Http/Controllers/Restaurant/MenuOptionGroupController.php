<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\MenuOptionGroup;
use App\Models\MenuOptionValue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MenuOptionGroupController extends Controller
{
    public function index(): View
    {
        $groups = MenuOptionGroup::with(['values', 'menuItems'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $menuItems = MenuItem::with('category')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('restaurant.menu.options.index', compact('groups', 'menuItems'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'selection_type' => 'required|in:single,multiple',
            'is_required' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'values' => 'required|array|min:1',
            'values.*.label' => 'required|string|max:120',
            'values.*.price_delta' => 'required|numeric|min:0',
            'values.*.sort_order' => 'nullable|integer|min:0|max:9999',
            'menu_item_ids' => 'nullable|array',
            'menu_item_ids.*' => 'uuid|exists:menu_items,id',
        ]);

        DB::transaction(function () use ($data) {
            $group = MenuOptionGroup::create([
                'name' => $data['name'],
                'selection_type' => $data['selection_type'],
                'is_required' => (bool) ($data['is_required'] ?? false),
                'is_active' => (bool) ($data['is_active'] ?? true),
                'sort_order' => (int) ($data['sort_order'] ?? 0),
            ]);

            foreach ($data['values'] as $value) {
                MenuOptionValue::create([
                    'menu_option_group_id' => $group->id,
                    'label' => $value['label'],
                    'price_delta' => $value['price_delta'],
                    'is_active' => true,
                    'sort_order' => (int) ($value['sort_order'] ?? 0),
                ]);
            }

            if (!empty($data['menu_item_ids'])) {
                $attach = [];
                foreach ($data['menu_item_ids'] as $index => $menuItemId) {
                    $attach[$menuItemId] = ['sort_order' => $index];
                }
                $group->menuItems()->sync($attach);
            }
        });

        return back()->with('success', __('general.restaurant.messages.option_group_created'));
    }

    public function update(Request $request, MenuOptionGroup $menuOptionGroup): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'selection_type' => 'required|in:single,multiple',
            'is_required' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'values' => 'required|array|min:1',
            'values.*.id' => 'nullable|uuid|exists:menu_option_values,id',
            'values.*.label' => 'required|string|max:120',
            'values.*.price_delta' => 'required|numeric|min:0',
            'values.*.is_active' => 'nullable|boolean',
            'values.*.sort_order' => 'nullable|integer|min:0|max:9999',
            'menu_item_ids' => 'nullable|array',
            'menu_item_ids.*' => 'uuid|exists:menu_items,id',
        ]);

        DB::transaction(function () use ($data, $menuOptionGroup) {
            $menuOptionGroup->update([
                'name' => $data['name'],
                'selection_type' => $data['selection_type'],
                'is_required' => (bool) ($data['is_required'] ?? false),
                'is_active' => (bool) ($data['is_active'] ?? false),
                'sort_order' => (int) ($data['sort_order'] ?? 0),
            ]);

            $existingIds = $menuOptionGroup->values()->pluck('id')->all();
            $submittedIds = [];

            foreach ($data['values'] as $value) {
                if (!empty($value['id'])) {
                    $submittedIds[] = $value['id'];
                    MenuOptionValue::where('id', $value['id'])->update([
                        'label' => $value['label'],
                        'price_delta' => $value['price_delta'],
                        'is_active' => (bool) ($value['is_active'] ?? true),
                        'sort_order' => (int) ($value['sort_order'] ?? 0),
                    ]);
                    continue;
                }

                $created = MenuOptionValue::create([
                    'menu_option_group_id' => $menuOptionGroup->id,
                    'label' => $value['label'],
                    'price_delta' => $value['price_delta'],
                    'is_active' => (bool) ($value['is_active'] ?? true),
                    'sort_order' => (int) ($value['sort_order'] ?? 0),
                ]);
                $submittedIds[] = $created->id;
            }

            $toDelete = array_diff($existingIds, $submittedIds);
            if (!empty($toDelete)) {
                MenuOptionValue::whereIn('id', $toDelete)->get()->each(fn($v) => $this->softDelete($v));
            }

            $attach = [];
            foreach (($data['menu_item_ids'] ?? []) as $index => $menuItemId) {
                $attach[$menuItemId] = ['sort_order' => $index];
            }
            $menuOptionGroup->menuItems()->sync($attach);
        });

        return back()->with('success', __('general.restaurant.messages.option_group_updated'));
    }

    public function destroy(MenuOptionGroup $menuOptionGroup): RedirectResponse
    {
        $menuOptionGroup->update(['is_active' => false]);
        $this->softDelete($menuOptionGroup);
        $menuOptionGroup->values->each(fn($v) => $this->softDelete($v));

        return back()->with('success', __('general.restaurant.messages.option_group_archived'));
    }
}

