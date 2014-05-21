<?php
/**
 * ownCloud - Calendar App
 *
 * @author Georg Ehrke
 * @copyright 2014 Georg Ehrke <oc.list@georgehrke.com>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
namespace OCA\Calendar\BusinessLayer;

use OCP\AppFramework\Http;

use OCA\Calendar\Db\Subscription;
use OCA\Calendar\Db\SubscriptionCollection;
use OCA\Calendar\Db\SubscriptionMapper;

use OCA\Calendar\Db\DoesNotExistException;
use OCA\Calendar\Db\MultipleObjectsReturnedException;

class SubscriptionBusinessLayer extends BusinessLayer {

	/**
	 * @var SubscriptionMapper
	 */
	protected $mapper;


	/**
	 * @param string $userId
	 * @param integer $limit
	 * @param integer $offset
	 * @return SubscriptionCollection
	 */
	public function findAll($userId, $limit, $offset) {
		return $this->mapper->findAll($userId, $limit, $offset);
	}


	/**
	 * @param string $userId
	 * @return integer
	 */
	public function count($userId) {
		return $this->mapper->count($userId);
	}


	/**
	 * @param string $userId
	 * @param string $type
	 * @param integer $limit
	 * @param integer $offset
	 * @return SubscriptionCollection
	 */
	public function findAllByType($userId, $type, $limit, $offset) {
		return $this->mapper->findAllByType($userId, $type, $limit, $offset);
	}


	/**
	 * @param string $userId
	 * @param string $type
	 * @return integer
	 */
	public function countByType($userId, $type) {
		return $this->mapper->countByType($userId, $type);
	}


	/**
	 * @param string $name
	 * @param string $userId
	 * @throws BusinessLayerException
	 * @return Subscription
	 */
	public function find($name, $userId) {
		try {
			return $this->mapper->find($name, $userId);
		} catch(DoesNotExistException $ex) {
			throw new BusinessLayerException($ex->getMessage(), Http::STATUS_NOT_FOUND, $ex);
		} catch(MultipleObjectsReturnedException $ex) {
			throw new BusinessLayerException($ex->getMessage(), HTTP::STATUS_INTERNAL_SERVER_ERROR, $ex);
		}
	}


	/**
	 * @param string $name
	 * @param string $userId
	 * @return bool
	 */
	public function doesExist($name, $userId) {
		return $this->mapper->doesExist($name, $userId);
	}


	/**
	 * @param string $name
	 * @param string $type
	 * @param string $userId
	 * @return mixed
	 */
	public function doesExistOfType($name, $type, $userId) {
		return $this->mapper->doesExistOfType($name, $type, $userId);
	}


	/**
	 * @param Subscription $subscription
	 * @throws BusinessLayerException
	 * @return Subscription
	 */
	public function create(Subscription $subscription) {
		$name = $subscription->getName();
		$userId = $subscription->getUserId();

		if (!$subscription->isValid()) {
			throw new BusinessLayerException('Subscription is not valid', Http::STATUS_UNPROCESSABLE_ENTITY);
		}

		if ($this->doesExist($name, $userId)) {
			throw new BusinessLayerException('Subscription with name already exists!', Http::STATUS_CONFLICT);
		}

		return $this->mapper->insert($subscription);
	}


	/**
	 * @param Subscription $subscription
	 * @param string $name
	 * @param string $userId
	 * @throws BusinessLayerException
	 * @return Subscription
	 */
	public function update(Subscription $subscription, $name, $userId) {
		if (!$subscription->isValid()) {
			throw new BusinessLayerException('Subscription is not valid', Http::STATUS_UNPROCESSABLE_ENTITY);
		}

		$oldSubscription = $this->find($name, $userId);
		$subscription = $oldSubscription->overwriteWith($subscription);

		$this->mapper->update($subscription);

		return $subscription;
	}


	/**
	 * @param Subscription $subscription
	 */
	public function delete(Subscription $subscription) {
		$this->mapper->delete($subscription);
	}
}