<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\PayrollLine;
use App\Models\PayrollRun;
use App\Models\User;
use App\Services\AccountingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PayrollController extends Controller
{
    public function index(): View
    {
        $payrolls = PayrollRun::with(['preparer', 'approver'])
            ->latest()
            ->paginate(20);

        return view('accounting.payroll.index', compact('payrolls'));
    }

    public function create(): View
    {
        $staff = User::whereHas('role')->where('is_active', true)->with('role')->get();
        return view('accounting.payroll.create', compact('staff'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'period_month'           => 'required|string|regex:/^\d{4}-\d{2}$/',
            'pay_date'               => 'required|date',
            'notes'                  => 'nullable|string',
            'lines'                  => 'required|array|min:1',
            'lines.*.user_id'        => 'required|uuid|exists:users,id',
            'lines.*.basic_salary'   => 'required|numeric|min:0',
            'lines.*.allowances'     => 'nullable|numeric|min:0',
        ]);

        abort_if(
            PayrollRun::where('period_month', $data['period_month'])->exists(),
            422,
            "Payroll for {$data['period_month']} already exists."
        );

        $run = DB::transaction(function () use ($data) {
            $run = PayrollRun::create([
                'period_month' => $data['period_month'],
                'pay_date'     => $data['pay_date'],
                'notes'        => $data['notes'] ?? null,
                'status'       => 'draft',
                'prepared_by'  => auth()->id(),
            ]);

            foreach ($data['lines'] as $line) {
                $user        = User::with('role')->findOrFail($line['user_id']);
                $basic       = (float) $line['basic_salary'];
                $allowances  = (float) ($line['allowances'] ?? 0);
                $gross       = $basic + $allowances;

                // Tanzania NSSF: Employee 5%, Employer 15%
                $nssf_employee = round($gross * 0.05, 2);
                $nssf_employer = round($gross * 0.15, 2);

                // Tanzania PAYE bands (2024)
                $paye = $this->calculatePaye($gross);

                $net = $gross - $nssf_employee - $paye;

                PayrollLine::create([
                    'payroll_run_id'   => $run->id,
                    'user_id'          => $user->id,
                    'staff_name'       => $user->name,
                    'role'             => $user->role->name,
                    'basic_salary'     => $basic,
                    'allowances'       => $allowances,
                    'gross_salary'     => $gross,
                    'nssf_employee'    => $nssf_employee,
                    'nssf_employer'    => $nssf_employer,
                    'paye'             => $paye,
                    'net_salary'       => $net,
                ]);
            }

            $run->recalculate();
            return $run;
        });

        return redirect()
            ->route('accounting.payroll.show', $run)
            ->with('success', "Payroll {$run->reference_no} created.");
    }

    public function show(PayrollRun $payrollRun): View
    {
        $payrollRun->load(['lines.user', 'preparer', 'approver']);
        return view('accounting.payroll.show', compact('payrollRun'));
    }

    // POST — approve payroll → post to journal
    public function approve(PayrollRun $payrollRun, AccountingService $accounting): RedirectResponse
    {
        abort_if($payrollRun->status !== 'draft', 422, 'Only draft payrolls can be approved.');

        DB::transaction(function () use ($payrollRun, $accounting) {
            $payrollRun->update([
                'status'      => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // Post to accounting journal
            $accounting->postPayroll(
                reference:     $payrollRun->reference_no,
                payrollId:     $payrollRun->id,
                grossSalary:   (float) $payrollRun->total_gross,
                nssf_employer: (float) $payrollRun->total_nssf_employer,
                netSalary:     (float) $payrollRun->total_net,
                nssf_payable:  (float) ($payrollRun->total_nssf_employee + $payrollRun->total_nssf_employer),
                paye_payable:  (float) $payrollRun->total_paye,
                actorId:       auth()->id()
            );
        });

        return redirect()
            ->route('accounting.payroll.show', $payrollRun)
            ->with('success', "Payroll {$payrollRun->reference_no} approved and posted to ledger.");
    }

    // Tanzania PAYE calculation (TRA bands 2024)
    private function calculatePaye(float $monthlyGross): float
    {
        $annual = $monthlyGross * 12;
        $paye   = 0;

        if ($annual <= 2_040_000)      $paye = 0;
        elseif ($annual <= 4_320_000)  $paye = ($annual - 2_040_000) * 0.08;
        elseif ($annual <= 6_480_000)  $paye = 182_400 + ($annual - 4_320_000) * 0.20;
        elseif ($annual <= 8_640_000)  $paye = 614_400 + ($annual - 6_480_000) * 0.25;
        else                           $paye = 1_154_400 + ($annual - 8_640_000) * 0.30;

        return round($paye / 12, 2); // monthly PAYE
    }
}
