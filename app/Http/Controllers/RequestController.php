<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RequestController extends Controller
{

    public function index(Request $request)
    {


        try {
            if($request->keyPasswordHash!= env('API_KEY') || empty(env('API_KEY'))){
                abort(403, 'Não autorizado');
            }

            //tabela primarias
            $table = DB::table($request->table);

            $table = $table->limit($request->limit ?? 100);
            //colocar os inner join
            $innerTables = $request->joins;
            if (!empty($innerTables)) {
                foreach ($innerTables as $innerTable) {
                    if (!empty($request->conectColumn[$innerTable])) {
                        $table = $table->join($innerTable, $request->conectColumn[$innerTable]->first, $request->conectColumn[$innerTable]->operator, $request->conectColumn[$innerTable]->second);
                    }
                }
            }

            //colocar os left join
            $leftTables = $request->leftJoins;
            if (!empty($leftTables)) {
                foreach ($leftTables as $leftTable) {
                    if (!empty($request->conectColumn[$leftTable])) {
                        $table = $table->leftJoin($leftTable, $request->conectColumn[$leftTable]->first, $request->conectColumn[$leftTable]->operator, $request->conectColumn[$leftTable]->second);
                    }

                }
            }

            //colocar os right join
            $rightTables = $request->rightJoins;
            if (!empty($rightTables)) {
                foreach ($rightTables as $rightTable) {
                    if (!empty($request->conectColumn[$rightTable])) {
                        $table = $table->rightJoin($rightTable, $request->conectColumn[$rightTable]->first, $request->conectColumn[$rightTable]->operator, $request->conectColumn[$rightTable]->second);
                    }
                }
            }

            //colocar os right join
            $crossTables = $request->crossJoins;
            if (!empty($crossTables)) {
                foreach ($crossTables as $crossTable) {
                    $table = $table->crossJoin($crossTable);
                }
            }

            //filtra where and where
            $whereAnd = $request->whereAnd;
            if (!empty($whereAnd)) {
                foreach ($whereAnd as $where) {
                    $table = $table->where($where[0],$where[1],$where[2]);
                }
            }

            //filtra where or where
            $whereOr = $request->whereOr;
            if (!empty($whereOr)) {
                foreach ($whereOr as $where) {
                    $table = $table->orWhere($where[0],$where[1],$where[2]);
                }
            }
            //filtra where Not
            $whereNot = $request->whereNot;
            if (!empty($whereNot)) {
                foreach ($whereNot as $where) {
                    $table = $table->whereNot($where[0],$where[1],$where[2]);
                }
            }
            //filtra where Not in
            $whereNotIn = $request->whereNotIn;
            if (!empty($whereNotIn)) {
                foreach ($whereNotIn as $where) {
                    $table = $table->whereNotIn($where[0],$where[1]);
                }
            }

            //filtra where Not in
            $whereBetween= $request->whereBetween;
            if (!empty($whereBetween)) {
                foreach ($whereBetween as $where) {
                    $table = $table->whereBetween($where[0],$where[1],$where[2]);
                }
            }

            //filtra where or where
            $whereIn = $request->whereIn;
            if (!empty($whereIn)) {
                foreach ($whereIn as $where) {
                    $table = $table->whereIn($where[0],$where[1]);
                }
            }

            //coloca em ordem
            $orderBys = $request->orderBys;
            if (!empty($orderBys)) {
                foreach ($orderBys as $orderBy) {
                    $table = $table->orderBy($orderBy[0],$orderBy[1]);
                }
            }

            //coloca em ordem
            $groupBys = $request->groupBys;
            if (!empty($groupBys)) {
                foreach ($groupBys as $groupBy) {
                    $table = $table->orderBy($groupBy[0],$groupBy[1]);
                }
            }

            $columns = $request->columns;
            if (!empty($columns)) {
                foreach ($columns as $column) {
                    $queryColumns[] = DB::raw($column);
                }
                $data = $table->select($queryColumns)->get();
            } else {
                $data = $table->get();
            }


            $response = ['success' => true, 'message' => 'Consulta feito com sucesso', 'limit' => $request->limit ?? 100, 'data' => $data];
        } catch (Exception $e) {
            $response = ['success' => false, 'message' => $e->getMessage()];
            Log::error($e->getMessage() . ' linha: ' . $e->getLine() . ' Arquivo: ' . $e->getFile());
        }
        return $response;
    }

    public function update(Request $request)
    {
        try {
            if($request->key!= env('API_KEY') || empty(env('API_KEY'))){
                abort(403, 'Não autorizado');
            }

            $dataColumns =$request->dataColumns;
            $dataColumns['updated_at'] = Carbon::now();
            $data = DB::table($request->table)
                ->where('id',$request->id)
                ->update($dataColumns);

            $response = ['success' => true, 'message' => 'Atualização feito com sucesso'];
        } catch (Exception $e) {
            $response = ['success' => false, 'message' => $e->getMessage()];
            Log::error($e->getMessage() . ' linha: ' . $e->getLine() . ' Arquivo: ' . $e->getFile());
        }
        return $response;
    }

    public function store(Request $request)
    {


        try {
            if($request->key!= env('API_KEY') || empty(env('API_KEY'))){
                abort(403, 'Não autorizado');
            }
            $dataColumns =$request->dataColumns;
            $dataColumns['created_at'] = Carbon::now();
            $dataColumns['updated_at'] = Carbon::now();
            $data = DB::table($request->table)
                ->insert($dataColumns);

            $response = ['success' => true, 'message' => 'Inserção feita com sucesso'];
        } catch (Exception $e) {
            $response = ['success' => false, 'message' => $e->getMessage()];
            Log::error($e->getMessage() . ' linha: ' . $e->getLine() . ' Arquivo: ' . $e->getFile());
        }
        return $response;

    }

    public function delete(Request $request)
    {


        try {
            if($request->keyPasswordHash!= env('API_KEY') || empty(env('API_KEY'))){
                abort(403, 'Não autorizado');
            }

            DB::table($request->table)
                ->where('id',$request->id)
                ->delete();

            $response = ['success' => true, 'message' => 'Apagado com sucesso'];
        } catch (Exception $e) {
            $response = ['success' => false, 'message' => $e->getMessage()];
            Log::error($e->getMessage() . ' linha: ' . $e->getLine() . ' Arquivo: ' . $e->getFile());
        }
        return $response;
    }


}
