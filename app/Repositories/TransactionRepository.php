<?php

namespace App\Repositories;


use App\Models\Transactions;
use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;

class TransactionRepository extends BaseRepository
{
    public function setModel()
    {
        return Transactions::class;
    }


    function transactionalUsers($user_limit = 3, $offset = 0, $from_date)
    {
        $query = '
        select users.* ,COUNT(accounts.user_id) as number_of_transaction FROM transactions 
        Join cards ON transactions.origin_card_id =cards.id
        JOIN accounts ON accounts.id =cards.account_id 
        Join users on accounts.user_id=users.id
        where transactions.parrent_transaction_id NOT NULL
        and transactions.created_at >= ?
        GROUP by accounts.user_id 
        ORDER BY number_of_transaction DESC
        LIMIT ? OFFSET ?
        ';
        return DB::select($query, [$from_date, $user_limit, $offset]);
    }

    function usertransactions($user_id, $transaction_limit = 10)
    {

        $query = ("select transactions.* FROM transactions 
        Join cards ON transactions.origin_card_id =cards.id
        JOIN accounts ON accounts.id =cards.account_id 
        where transactions.parrent_transaction_id NOT NULL
        and accounts.user_id in (?)
        ORDER BY transactions.created_at DESC
        LIMIT ?
        ");
        return  DB::select($query, [$user_id, $transaction_limit]);
    }
}
