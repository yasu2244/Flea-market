<?php

return [
    'required' => ':attribute を入力してください。',
    'email' => ':attribute は正しいメールアドレス形式で入力してください。',
    'unique' => ':attribute はすでに登録されています。',
    'min' => [
        'string' => ':attribute は :min 文字以上で入力してください。',
    ],
    'confirmed' => ':attribute が確認用と一致しません。',
    'regex' => ':attribute の形式が正しくありません。',
    'mimes' => ':attribute は jpeg または png の形式でアップロードしてください。',
    'image' => ':attribute は画像ファイルを選択してください。',

    'attributes' => [
        'name' => 'お名前',
        'email' => 'メールアドレス',
        'password' => 'パスワード',
        'postal_code' => '郵便番号',
        'address' => '住所',
        'building' => '建物名',
        'profile_image' => 'プロフィール画像',
    ],
];
