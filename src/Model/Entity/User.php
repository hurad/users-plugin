<?php

namespace Pie\Users\Model\Entity;

use Cake\ORM\Entity;

/**
 * User Entity
 *
 * @package Users\Model\Entity
 */
class User extends Entity
{
    /**
     * Get user details
     *
     * @param null|string $key     Detail key
     * @param null|mixed  $default Detail default
     *
     * @return array|bool|null Return false when "details" property not exists,
     * return array when not select specific detail and
     * return null when not defined default value
     */
    public function getDetails($key = null, $default = null)
    {
        if (!isset($this->_properties['details'])) {
            return false;
        }

        $tmpDetail = [];
        /** @var $detail Entity */
        foreach ($this->_properties['details'] as $detail) {
            $tmpDetail[$detail->get('key')] = $detail;
        }

        if (is_null($key)) {
            return $tmpDetail;
        } elseif (array_key_exists($key, $tmpDetail)) {
            return $tmpDetail[$key]->get('value');
        } else {
            return $default;
        }
    }
}
