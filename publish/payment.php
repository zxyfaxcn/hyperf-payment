<?php
return [
    'payment' => [
        'ali' => [
            'driver'  => '',
            'options' => [
                'app_id'          => '2016073100130857',
                'sign_type'       => 'RSA2',
                'ali_public_key'  => '', //文件目录或者字符串
                'rsa_private_key' => '', //文件目录或者字符串
                'limit_pay'       => [
                    //'balance',// 余额
                    //'moneyFund',// 余额宝
                    //'debitCardExpress',// 	借记卡快捷
                    //'creditCard',//信用卡
                    //'creditCardExpress',// 信用卡快捷
                    //'creditCardCartoon',//信用卡卡通
                    //'credit_group',// 信用支付类型（包含信用卡卡通、信用卡快捷、花呗、花呗分期）
                ], // 用户不可用指定渠道支付当有多个渠道时用“,”分隔
                // 与业务相关参数
                'notify_url'      => 'https://dayutalk.cn/notify/ali',
                'return_url'      => '',
                'fee_type'        => 'CNY', // 货币类型  当前仅支持该字段
            ]
        ],
        'wx'  => [
            'driver'  => '',
            'options' => [
                'app_id'       => 'wxxxxxxxx',  // 公众账号ID
                'mch_id'       => '123123123', // 商户id
                'md5_key'      => '23423423dsaddasdas', // md5 秘钥
                'app_cert_pem' => '', //文件地址
                'app_key_pem'  => '',//文件地址
                'sign_type'    => 'MD5', // MD5  HMAC-SHA256
                'limit_pay'    => [
                    //'no_credit',
                ], // 指定不能使用信用卡支付   不传入，则均可使用
                'fee_type'     => 'CNY', // 货币类型  当前仅支持该字段
                'notify_url' => 'https://dayutalk.cn/v1/notify/wx',
                'redirect_url' => '', // 如果是h5支付，可以设置该值，返回到指定页面
            ]
        ],
    ]
];
