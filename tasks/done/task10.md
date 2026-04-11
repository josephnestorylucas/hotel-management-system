error while running migration 

  Illuminate\Database\QueryException

  SQLSTATE[42804]: Datatype mismatch: 7 ERROR:  foreign key constraint "receipts_cashier_id_foreign" cannot be implemented
DETAIL:  Key columns "cashier_id" and "id" are of incompatible types: bigint and uuid. (Connection: pgsql, Host: 127.0.0.1, Port: 5432, Database: hms_db, SQL: alter table "
receipts" add constraint "receipts_cashier_id_foreign" foreign key ("cashier_id") references "users" ("id") on delete set null)

  at vendor\laravel\framework\src\Illuminate\Database\Connection.php:838
    834▕             $exceptionType = $this->isUniqueConstraintError($e)
    835▕                 ? UniqueConstraintViolationException::class
    836▕                 : QueryException::class;
    837▕
  ➜ 838▕             throw new $exceptionType(
    839▕                 $this->getNameWithReadWriteType(),
    840▕                 $query,
    841▕                 $this->prepareBindings($bindings),
    842▕                 $e,

  1   vendor\laravel\framework\src\Illuminate\Database\Connection.php:584
      PDOException::("SQLSTATE[42804]: Datatype mismatch: 7 ERROR:  foreign key constraint "receipts_cashier_id_foreign" cannot be implemented
DETAIL:  Key columns "cashier_id" and "id" are of incompatible types: bigint and uuid.")

  2   vendor\laravel\framework\src\Illuminate\Database\Connection.php:584
      PDOStatement::execute()

