<?php

class Test_Helpers_Arr extends \TestCase
{
    protected function get_source_array(array $nested = null)
    {
        $source = [
            'key' => 'value',
            'number' => 132345,
            'mbstring' => 'Тест на русском языке'
        ];

        if (is_array($nested)) {
            $source['nested'] = $nested;
        }

        return $source;
    }

    public function testSimilar()
    {
        $nested = [
            'key' => 'value'
        ];

        $source = $this->get_source_array($nested);

        // сверяем два одинаковых массива
        $result = \Cloudstash\Point\Helper\Arr::similar($source, $source);
        $this->assertTrue($result, 'Not similar to equals arrays');

        // сверяем два различных массива
        $result = \Cloudstash\Point\Helper\Arr::similar($source, $nested);
        $this->assertFalse($result, 'Similar to NOT equals arrays');
    }

    public function testGet()
    {
        $nested = [
            'key' => 'value'
        ];

        $source = $this->get_source_array($nested);

        // получаем по ключу значение (строка)
        $result = \Cloudstash\Point\Helper\Arr::get($source, 'key');
        $this->assertEquals($result, 'value', 'Wrong value for key "key"');

        // получаем по ключу значение (число)
        $result = \Cloudstash\Point\Helper\Arr::get($source, 'number');
        $this->assertEquals($result, 132345, 'Wrong value for key "number"');

        // получаем по ключу значение (многобайтовая строка)
        $result = \Cloudstash\Point\Helper\Arr::get($source, 'mbstring');
        $this->assertEquals($result, 'Тест на русском языке', 'Wrong value for key "mbstring"');

        // при отсутствующем ключе, должен возвращаться null
        $result = \Cloudstash\Point\Helper\Arr::get($source, 'undefined');
        $this->assertEquals($result, null, 'Wrong default value (null)');

        // при отсутствующем ключе, должено вернуться объявленное значение по умолчанию (число)
        $result = \Cloudstash\Point\Helper\Arr::get($source, 'undefined', 1000);
        $this->assertEquals($result, 1000, 'Wrong default value (number)');

        // получаем по ключу вложеный массив
        $result = \Cloudstash\Point\Helper\Arr::get($source, 'nested');
        $result = \Cloudstash\Point\Helper\Arr::similar($result, $nested);
        $this->assertTrue($result, 'Wrong nested array');
    }
}