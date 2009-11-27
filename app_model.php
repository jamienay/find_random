<?php
class AppModel extends Model {
    
    /**
     * __findRandom()
     *
     * Find a list of records ordered by rank.
     * Instead of executing a __findList() query to get the list of IDs,
     * you can pass an array of IDs via the $options['suppliedList']
     * argument.
     *
     * Two queries are executed, first a find('list') to generate a list of primary
     * keys, and then either a find('all') or find('first') depending on the return 
     * amount specified (default 1). 
     *
     * Pass find options to each query using the $options['list'] and $options['find']
     * arguments. 
     * 
     * Specify $options['amount'] as the maximum number of random items that should
     * be returned.
     *
     * If you already have an array of IDs(/primary keys), you can skip the find('list')
     * query by passing the array as $options['suppliedList'].
     *
     * @access  private
     * @param   $options  array of standard and function-specific find options.
     * @return  array
     */
    function __findRandom($options = array()) {
        if (!isset($options['amount'])) {
            $amount = 1;
        } else {
            $amount = $options['amount'];
        }

        $findOptions = array();
        if (isset($options['find'])) {
            $findOptions = array_merge($findOptions, $options['find']);
        }
            
        if (!isset($options['suppliedList'])) {
            $listOptions = array();
            if (isset($options['list'])) {
                $listOptions = array_merge($listOptions, $options['list']);
            }
            
            $list = $this->find('list', $listOptions);
        } else {
            $list = $options['suppliedList'];
            $list = array_flip($list);
        }        
        
        // Just a little failsafe.
        if (count($list) < 1) {
            return $list;
        }
        
        $originalAmount = null;
        if ($amount > count($list)) {
            $originalAmount = $amount;
            $amount = count($list);
        }
            
        $id = array_rand($list, $amount);

        if (is_array($id)) {
            shuffle($id);
        }

        if (!isset($findOptions['conditions'])) {
            $findOptions['conditions'] = array();
        }

        $findOptions['conditions'][$this->alias.'.'.$this->primaryKey] = $id;
        if ($amount == 1 && !$originalAmount) {
            return $this->find('first', $findOptions);
        } else {
            return $this->find('all', $findOptions);
        }
    }
?>