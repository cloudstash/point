<?php

namespace Cloudstash\Point\HTTP;

/**
 * Class Uri
 * @package Helper
 * @author Viktor Kulikov <bezdoom@gmail.com>
 *
 * С помощью текущего класса можно удобно работать со сылками.
 */
class Uri
{
    protected $url = null;
    protected $readonly = null;
    protected $segments = [];

    /**
     * @param string $url Ссылка. Если не указана (null), то берется текущая.
     * @param bool $readonly Инициализирет объект только для чтения. Функции обновления сегментов будут не доступны.
     */
    function __construct($url = null, $readonly = true)
    {
        $this->url = $url;
        $this->readonly = $readonly;

        if (!$this->url)
        {
            $protocol = $this->IsSecureConnection() ? "https://" : "http://";
            $this->url = $protocol . $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
        }

        $this->segments = parse_url($this->url);
    }

    /**
     * @param string $segment Название сегмента
     *
     * @return string
     *
     * Возвращает знаечние сегмента теущего урла
     */
    public function getSegment($segment)
    {
        if (array_key_exists($segment, $this->segments))
            return $this->segments[$segment];

        return null;
    }

    /**
     * @param string $segment Название сегмента
     * @param mixed $value Новое значение сегмента
     *
     * @throws \RuntimeException
     *
     * Обновляет знаечние текущего сегмента.
     * Если объект инициализирован только для чтения, то бросит исключние RuntimeException
     */
    public function setSegments($segment, $value)
    {
        if ($this->readonly)
            throw new \RuntimeException("Uri entity read only");

        if (!array_key_exists($segment, $value))
            throw new \RuntimeException("Segment {$segment} does not exist");

        $this->segments[$segment] = $value;
    }

    /**
     * @return string Текущий полный путь вместе с протоколом и доменом
     */
    public function getFullUri()
    {
        return $this->url;
    }

    /**
     * @return string Текущий полный путь от корня (домен не включен)
     */
    public function getRequestUri()
    {
        return $this->path . ( $this->query ? "?" . $this->query : "" );
    }

    /**
     * @return string Текущий путь без учета Гет-параметров
     */
    public function getRequestPath()
    {
        return $this->path;
    }

    /**
     * @return string Текущие гет-параметры
     */
    public function getRequestQuery()
    {
        return $this->query;
    }

    /**
     * @return bool Если ссылка использует ssl, то вернет true
     */
    public static function IsSecureConnection()
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443);
    }

    /**
     * @return bool
     */
    public function isRootDirectory()
    {
        return $this->getRequestPath() == "/";
    }

    /**
     * @magic
     * @param $key
     *
     * @return string
     */
    public function __get($key)
    {
        return $this->getSegment($key);
    }

    /**
     * @magic
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->setSegments($key, $value);
    }
}