<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace Hyperf\Payment\Helper;

class Rc4Encrypt
{
    protected $key;

    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * 设置key.
     *
     * @param $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * rc4加密算法.
     *
     * @param $data
     *
     * @throws \Exception
     * @return string
     */
    public function encrypt($data)
    {
        $cipher = $box[] = $key[] = '';

        $pwd_length = strlen($this->key);
        $data_length = strlen($data);

        for ($i = 0; $i < 256; ++$i) {
            $key[$i] = ord($this->key[$i % $pwd_length]);
            $box[$i] = $i;
        }

        for ($j = $i = 0; $i < 256; ++$i) {
            $j = ($j + $box[$i] + $key[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $data_length; ++$i) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;

            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;

            $k = $box[(($box[$a] + $box[$j]) % 256)];
            $cipher .= chr(ord($data[$i]) ^ $k);
        }

        return strtoupper(StrUtil::String2Hex($cipher));
    }
}
