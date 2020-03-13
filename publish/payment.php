<?php
declare(strict_types = 1);
return [
    'payment' => [
        'ali' => [
            'options' => [
                'app_id'          => '2016073100130857',
                'sign_type'       => 'RSA2',
                'ali_public_key'  => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAmBjJu2eA5HVSeHb7jZsuKKbPp3w0sKEsLTVvBKQOtyb7bjQRWMWBI7FrcwEekM1nIL+rDv71uFtgv7apMMJdQQyF7g6Lnn9niG8bT1ttB8Fp0eud5L97eRjFTOa9NhxUVFjGDqQ3b88o6u20HNJ3PRckZhNaFJJQzlahCpxaiIRX2umAWFkaeQu1fcjmoS3l3BLj8Ly2zRZAnczv8Jnkp7qsVYeYt01EPsAxd6dRZRw3uqsv9pxSvyEYA7GV7XL6da+JdvXECalQeyvUFzn9u1K5ivGID7LPUakdTBUDzlYIhbpU1VS8xO1BU3GYXkAaumdWQt7f+khoFoSw+x8yqQIDAQAB',
                //文件目录或者字符串
                'rsa_private_key' => 'MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQC/z+Ue/oS0GjO2myYrkdopw5qq6Ih/xlHBx0HBE0xA2dRinpMuZeI0LUUtN54UAUZbDz8rcaOCb0jeloeYolw54tadcIw4Q2hbdeJPplldJZyi1BDYtBJZvAveeRSidHdmBSUtOtCBXUBlJUP3I8/R4c34Ii4Pm/K4vmhwLf/zqZAedKGhYP6m5q+p8sfBHRPy97/KluLPiSTRFqGSRmd0IitUGK+KQ5qsAfJXyN1oVR4jBYaxfx7dWkTWmxAfNqtKfMvu2a5lH6hvClN+w4RUDBu3939bLjCYKcAomkv3QMquMP46m+D8Ny+3mGk5L9Ul4jyxlFTlV4L4JM3g/02xAgMBAAECggEBALZliwseHDLnd6V9g56K41ozlzBOTv6yJ6yNPgnLwAcrHLtq76p/V8smAVIuQTPkwnJ03S0CsumlyTVhDzAltG2XN14fWDdoYiQWxU3YccIRshFkd2CaW5jZKLA1k1moRqHM4r1P4FYjxshn12l7tHNwtdvvJL3THcxvxABovauFOVtznpRlnfJLjn2Lg+xNsxaYy3zL8L6nL7MXUWLKvmLiZn64PFcw7cf+9n2exRDswn0wDCpypGqOVVXVFeZaXTwmOoxgIUAZfAExdLtabGGCAz1lTsA0+r4DW2nSTe8CFy1Db+fcCTm+uQ3y6jDwuS3tB8V+PQKog3+ReZp/9sECgYEA/NEr+ln6DTy7u4rCWq7mixRJ1kaiAUph/hADrUwhkMiUapSMNAIXblFB+BQUjFZQmXEbcvz0Y70g9Zi9JCXVTiDTBe7jj/FK63MU0F9KY5OducpVV+RhSpNy/i1M2qeW4gO351PpPHUpRUYrGkYvAKktqrSOdBEWD3IeKLYDXxMCgYEAwjoavGjWzD9Xckbpb8yrQ+gHfLeWDKh7BgvoBGagyqbzIOZU9wg3dSQ2F5eMWDxWVRGqap3fIHxcA0/VMqXG1DrvSIUC4SE8Zys515fR00c9h3W3IugHnKgdYcV7nZrJoPZXlMjPOo39FCBnfbrUOgnKwxMlz3lVvC6465ODhKsCgYEAmUtTuTd5kTE0O+FFO6s1iztAEjc94D5z8JNRR3EUITAeHgn4gUiLYI7Qy1WRqA5mTMPyeuS6Ywe4xnJYrWRrVDY+/if9v7f1T5K2GirNdld5mb//w41tGMUTQt/A7AwWRvEuP4v3rnr0DVcgp4vK0EHEuO9GOUZq8+6kLtc+cBUCgYBFJ/kzEsVAjmEtkHA33ZExqaFY1+l2clrziTPAtWYVIiK5mSmxl9xfOliER/KxzDIVMigStEmpQH5ms3s/AGXuVVmz4aBn1rSyK2L6D9WnO9t9qv1dUW68aeOkV3OvZ1jZlj0S/flDaSEulGclDmvYinoGwX+aAyLy0VQIlUqj5wKBgHEUEf7YDnvw/IBnF1E4983/7zBx9skoHhpEZsh2+1or7LIw6z0m3lsNBnK0MZZBmW/7HwOtVfhXUUPbVrOJdi70YoMynX3gjK3LTXhzISheZgcNRKTqiJgVunPokJxQRyYcAfaQeuIm9O8cCPE1rZpNAzCdd4NSj83UZRm3YOmC',
                //文件目录或者字符串
                'limit_pay'       => [
                    //'balance',// 余额
                    //'moneyFund',// 余额宝
                    //'debitCardExpress',// 	借记卡快捷
                    //'creditCard',//信用卡
                    //'creditCardExpress',// 信用卡快捷
                    //'creditCardCartoon',//信用卡卡通
                    //'credit_group',// 信用支付类型（包含信用卡卡通、信用卡快捷、花呗、花呗分期）
                ],
                // 用户不可用指定渠道支付当有多个渠道时用“,”分隔
                // 与业务相关参数
                'notify_url'      => 'https://pay.jayjay.cn/notify/ali',
                'return_url'      => '',
                'fee_type'        => 'CNY',
                // 货币类型  当前仅支持该字段
            ]
        ],
        'wx'  => [
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
                'notify_url'   => 'https://dayutalk.cn/v1/notify/wx',
                'redirect_url' => '', // 如果是h5支付，可以设置该值，返回到指定页面
            ]
        ],
    ]
];
