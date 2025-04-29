<?php

return [
    'required'   => ':attribute を入力してください。',
    'email'      => ':attribute は正しいメールアドレス形式で入力してください。',
    'unique'     => ':attribute はすでに登録されています。',
    'exists'     => ':attribute が正しく選択されていません。',
    'numeric'    => ':attribute は数値で入力してください。',
    'array'      => ':attribute は配列で入力してください。',
    'min'        => [
        'string'  => ':attribute は :min 文字以上で入力してください。',
        'numeric' => ':attribute は :min 以上で入力してください。',
    ],
    'max'        => [
        'string'  => ':attribute は :max 文字以内で入力してください。',
        'numeric' => ':attribute は :max 以下で入力してください。',
    ],
    'confirmed'  => ':attribute が確認用と一致しません。',
    'regex'      => ':attribute の形式が正しくありません。',
    'mimes'      => ':attribute は jpeg または png の形式でアップロードしてください。',
    'image'      => ':attribute は画像ファイルを選択してください。',

    'attributes' => [
        'name'         => 'お名前',
        'email'        => 'メールアドレス',
        'password'     => 'パスワード',
        'postal_code'  => '郵便番号',
        'address'      => '住所',
        'building'     => '建物名',
        'profile_image'=> 'プロフィール画像',
        'content'      => '商品コメント',
        'payment_method'=> '支払い方法',
        'shipping_address'=> '配送先',
        'description'  => '商品説明',
        'image'        => '商品画像',
        'categories'   => '商品のカテゴリー',
        'status_id'    => '商品の状態',
        'price'        => '商品価格',
        'brand'        => 'ブランド',
    ],
];
