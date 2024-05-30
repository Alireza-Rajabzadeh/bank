<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Exceptions\GeneralException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;


abstract class BaseRepository
{
    protected $model;

    public function __construct()
    {
        $this->model =  app($this->setModel());
    }

    abstract public function setModel();

    #id need to set model manually
    public function setModelRepository(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Search in table.
     *
     * @param  array  $input
     * @param  boolean  $query : if want to retrive query set true
     * @return collection|query
     */
    public function search($input, $raw_query = false)
    {
        $input['sort'] ??= 'id';
        $input['order'] ??= 'desc';
        $input['null'] ??= [];
        $input['where'] ??= [];
        $input['columns'] ??= ['*'];
        $input['where_not_in'] ??= [];
        $input['where'] = array_filter($input['where']);
        $query = $this->model->where($input['where']);

        if (!empty($input['null'])) {
            foreach ($input['null'] as $is_null) {
                $query = $query->whereNull($is_null);
            }
        }

        if (!empty($input['where_not_in'])) {
            $query = $query->whereNotIn($input['where_not_in'][0], $input['where_not_in'][1]);
        }
        if (!empty($input['where_in'])) {

            foreach ($input['where_in'] as $field => $where_in_values) {

                if (is_string($where_in_values)) {
                    $where_in_values = explode(",", $where_in_values);
                }
                $query = $query->whereIn($field, $where_in_values);
            }
        }


        if (!empty($input['relations'])) {
            foreach ($input['relations'] as $relation) {
                $query = $query->with([$relation])->withCount("logs");
            }
        }

        if (!empty($input['count_relations'])) {
            foreach ($input['count_relations'] as $count_relations) {
                $query = $query->withCount([$count_relations]);
            }
        }

        if (!empty($input['like'])) {
            foreach ($input['like'] as $like_culomn => $like_value) {
                $query = $query->where($like_culomn, "like", "%" . $like_value . "%");
            }
        }

        if (!empty($input['group_by'])) {
            foreach ($input['group_by'] as $group_by_culomn) {

                $query = $query->withCount("logs");
            }
        }

        if (!empty($input['date']['from_date']) || !empty($input['date']['to_date'])) {
            if (!empty($input['date']['from_date']) && empty($input['date']['to_date'])) {
                $query = $query->where($input['date']['date_field'], ">=", $input['date']['from_date']);
            }
            if (empty($input['date']['from_date']) && !empty($input['date']['to_date'])) {
                $query = $query->where($input['date']['date_field'], "<=", $input['date']['to_date']);
            }
            if (!empty($input['date']['from_date']) && !empty($input['date']['to_date'])) {
                $query = $query->whereBetween($input['date']['date_field'], [$input['date']['from_date'], $input['date']['to_date']]);
            }
        }


        $query = $query->orderBy($input['sort'], $input['order']);

        if ($raw_query) {
            return $query;
        }
        return $query->get($input['columns']);
    }

    /**
     * Search in table and prepare response details like count.
     *
     * @param  array  $search : filter fields
     * @param  array  $update : update data
     */

    function index($search_inputs)
    {
        $search_inputs['columns'] ??= ['*'];

        $search_inputs['dump'] ??= false; #return all data

        $search_inputs['start_index'] ?? "0";
        $search_inputs['limit_index'] ?? "10";

        $query = $this->search($search_inputs, true);


        $result['total'] = $query->count();

        if (!($search_inputs['dump'])) {
            $query = $query->skip($search_inputs['start_index'])->take($search_inputs['limit_index']);
        }

        $result['result_count'] = ($query->get()->count());
        $result['result'] = $query->get($search_inputs['columns'])->toArray();
        return $result;
    }

    public function insert($input)
    {

        foreach ($input as $key => $value) {

            if ($value == "empty") {
                $input['key'] = null;
            }
        }
        return $this->model->create($input);
    }
}
