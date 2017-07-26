<?php namespace Tranquility\Data\ORM\Extensions\TablePrefix;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use LaravelDoctrine\ORM\Extensions\Extension;

class TablePrefixExtension implements Extension {
    /**
     * @param EventManager           $manager
     * @param EntityManagerInterface $em
     * @param Reader|null            $reader
     */
    public function addSubscribers(EventManager $manager, EntityManagerInterface $em, Reader $reader = null) {
        $listener = new TablePrefixListener($this->getPrefix($em->getConnection())); 
        $manager->addEventSubscriber($listener);
    }

    /**
     * @return array
     */
    public function getFilters() {
        return [];
    }

    /**
     * @param Connection $connection
     *
     * @return string
     */
    protected function getPrefix(Connection $connection) {
        $params = $connection->getParams();
        return array_get($params, 'prefix');
    }
}
