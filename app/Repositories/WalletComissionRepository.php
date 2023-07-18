<?php

namespace App\Repositories;

use App\Models\WalletComission;

class WalletComissionRepository 
{
    private $model;

    public function __construct()
    {
        $this->model = new WalletComission();
    }

    public function save(WalletComission $walletComission)
    {
        return $walletComission->save();
    }

}