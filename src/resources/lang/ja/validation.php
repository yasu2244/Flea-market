<?php

return [
    'required'   => ':attributeを入力してください',
    'email'      => ':attributeは正しいメールアドレス形式で入力してください',
    'unique'     => ':attributeはすでに登録されています',
    'exists'     => ':attributeが正しく選択されていません',
    'numeric'    => ':attributeは数値で入力してください',
    'array'      => ':attributeは配列で入力してください',
    'min'        => [
        'string'  => ':attributeは:min文字以上で入力してください',
        'numeric' => ':attributeは:min以上で入力してください',
    ],
    'max'        => [
        'string'  => ':attributeは:max文字以内で入力してください',
        'numeric' => ':attributeは max以下で入力してください',
    ],
    'confirmed'  => ':attributeと一致しません',
    'regex'      => ':attributeの形式が正しくありません',
    'mimes'      => ':attributeはjpegまたはpngの形式でアップロードしてください',
    'image'      => ':attributeは画像ファイルを選択してください',

    'custom' => [
        'payment_method' => [
            'required' => '支払い方法を選択してください',
        ],
        'shipping_address' => [
            'required' => '配送先を選択してください',
        ],
    ],


    'attributes' => [
        'name'         => 'お名前',
        'email'        => 'メールアドレス',
        'password'     => 'パスワード',
        'postal_code'  => '郵便番号',
        'address'      => '住所',
        'building'     => '建物名',
        'profile_image'=> 'プロフィール画像',
        'content'      => 'コメント',
        'payment_method'=> '支払い方法',
        'shipping_address'=> '配送先',
        'description'  => '商品説明',
        'image'        => '商品画像',
        'categories'   => '商品のカテゴリー',
        'status_id'    => '商品の状態',
        'price'        => '商品価格',
        'brand'        => 'ブランド',
        'body'         => '本文',
    ],
];
