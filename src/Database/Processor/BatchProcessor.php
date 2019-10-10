<?php

declare(strict_types=1);

namespace Yamilovs\SypexGeo\Database\Processor;

use Yamilovs\SypexGeo\Database\Config;
use Yamilovs\SypexGeo\Database\Reader;

class BatchProcessor extends AbstractProcessor
{
    /**
     * @var array
     */
    protected $byteIndexArray;

    /**
     * @var array
     */
    protected $mainIndexArray;

    public function __construct(Reader $reader, Config $config)
    {
        parent::__construct($reader, $config);

        $this->byteIndexArray = array_values(unpack("N*", $this->byteIndex));
        $this->mainIndexArray = str_split($this->mainIndex, static::MAIN_INDEX_BYTES);

        unset ($this->byteIndex, $this->mainIndex);
    }

    protected function readRawData(int $packFormat, int $start, int $length): string
    {
        // TODO: Same as FileProcessor::readRawData()
    }

    protected function getIndexBlockPosition(string $ip, int $min, int $max): int
    {
        $packedIp = $this->getPackedIp($ip);

        while ($max - $min > 8) {
            $offset = ($min + $max) >> 1;

            if ($packedIp > $this->mainIndexArray[$offset]) {
                $min = $offset;
            } else {
                $max = $offset;
            }
        }
        while ($packedIp > $this->mainIndexArray[$min] && $min++ < $max) {}

        return $min;
    }

    protected function getFirstByteIndexBlockRange(int $ip1n): array
    {
        return [$this->byteIndexArray[$ip1n-1], $this->byteIndexArray[$ip1n]];
    }

    protected function getDatabaseBlockPosition(string $ip, int $min, int $max): int
    {
        // TODO: Same as FileProcessor::getDatabaseBlockPosition()
    }
}