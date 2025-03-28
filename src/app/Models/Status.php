<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function items()
    {
        return $this->hasMany(Item::class);
>>>>>>> 041abce (商品一覧ページ・詳細ページ仮作成/詳細ページ関連のテーブル・シーディングファイルの追加とprodcut->itemに名前変更)
    }
}
