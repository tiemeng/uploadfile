<?php


namespace UploadFile;

/**
 * Class UploadFile
 * @package UploadFile
 * @method UploadFile dir(string $dir) 设置保存的目录
 * @method UploadFile size(int $size) 设置上传文件的大小
 * @method UploadFile type(array $type) 设置上传文件的大小
 * @method UploadFile sub(bool $isSub) 是否设置子目录
 *
 */
class UploadFile
{
    /**
     * @var array
     */
    private $_config = [
        'size' => 2048, //单位byte
        'type' => [
            'image/jpeg',
            'image/jpg'
        ],
        'sub' => false
    ];

    /**
     * 文件上传
     * @param array $fileInfo
     * @return string
     * @throws \Exception
     */
    public function upload(array $fileInfo)
    {
        if (empty($fileInfo)) {
            throw new \Exception('上传文件不能为空');
        }
        if (!$this->_checkConfig('dir')) {
            throw new \Exception('请设置目标地址');
        }
        /**
         * 1、检测文件大小是否超过限制
         * 2、检测是否支持文件类型
         * 3、目录是否存在且可写
         */
        $fileSize = $fileInfo['size'] ?? 0;
        if ($this->_checkSize($fileSize) < 0) {
            throw new \Exception('大小超过限制');
        }
        if (!$this->_checkType($fileInfo['type'])) {
            throw new \Exception('不支持改类型文件的上传');
        }

        try {
            $this->_mkdir();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        $filename = $fileInfo['tmp_name'];
        $ext = $this->_getExt($fileInfo['name']);
        $newFilename = uniqid().'.'.$ext;
        $destination = $this->_config['dir'] . DIRECTORY_SEPARATOR . $newFilename;
        if (move_uploaded_file($filename, $destination)) {
            return $newFilename;
        }

        throw new \Exception('上传文件失败');

    }

    public function __call($name, $arguments): self
    {
        $this->_config[$name] = $arguments[0];
        return $this;
    }


    private function _checkConfig(string $key): bool
    {
        return isset($this->_config[$key]);
    }

    /**
     * 检测上传文件类型是否正确
     * @param string $type
     * @return bool
     */
    private function _checkType(string $type): bool
    {
        $typeArr = $this->_config['type'];
        return in_array($type, $typeArr);
    }

    /**
     * 创建目录
     * @return bool
     * @throws \Exception
     */
    private function _mkdir()
    {
        $dir = $this->_config['dir'];
        if ($this->_config['sub']) {
            $dir .= DIRECTORY_SEPARATOR . date("Ymd");
            $this->_config['dir'] = $dir;
        }
        if (is_dir($dir)) {
            return true;
        }
        if (!mkdir($dir, 0777, true)) {
            throw new \Exception('创建目录失败');
        }
    }

    /**
     * 获取文件扩展名
     * @param string $filename
     * @return string
     */
    private function _getExt(string $filename):string{
        return pathinfo($filename,PATHINFO_EXTENSION);
    }

    /**
     * 检测大小是否超过限制
     * @param int $size
     * @return int
     */
    private function _checkSize(int $size): int
    {
        return $this->_config['size'] <=> $size;
    }

}
