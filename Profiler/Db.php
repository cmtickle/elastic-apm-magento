<?php

namespace Cmtickle\ElasticApm\Profiler;

class Db extends \Magento\Framework\Model\ResourceModel\Db\Profiler
{
    /**
     * @inheirtDoc
     */
    public function queryStart($queryText, $queryType = null)
    {
        $result = parent::queryStart($queryText, $queryType);

        if ($result !== null) {
            $queryTypeParsed = $this->_parseQueryType($queryText);
            $timerName = $this->_getTimerName($queryTypeParsed);

            $tags = [];

            // connection type to database
            $typePrefix = '';
            if ($this->_type) {
                $tags['group'] = $this->_type;
                $typePrefix = $this->_type . ':';
            }

            // sql operation
            $tags['operation'] = $typePrefix . $queryTypeParsed;

            $tags['statement'] = $queryText;

            // database host
            if ($this->_host) {
                $tags['host'] = $this->_host;
            }

            \Magento\Framework\Profiler::start($timerName, $tags);
        }

        return $result;
    }
}
