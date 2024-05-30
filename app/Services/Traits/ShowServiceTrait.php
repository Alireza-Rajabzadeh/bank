<?php

namespace App\Services\Traits;


trait ShowServiceTrait
{


    public function getDefaultSearchParams($index_data)
    {
        $search_data['limit_index'] = $index_data['limit_index'] ?? 10;
        $search_data['start_index'] = $index_data['start_index'] ?? 0;
        $search_data['dump'] = $index_data['dump'] ?? false;
        $search_data['order'] = $index_data['order'] ?? "DESC";
        $search_data['sort'] = $index_data['sort'] ?? 'id';

        return $search_data;

    }
    public function getDefaultDateParams()
    {

        $date_clause_columns = [
            "from_date",
            "to_date",
            "date_field",
        ];

        return $date_clause_columns;
    }
}
