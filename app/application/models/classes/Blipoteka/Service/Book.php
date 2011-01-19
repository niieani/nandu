<?php

/**
 * Blipoteka.pl
 *
 * LICENSE
 *
 * This source file is subject to the Simplified BSD License
 * that is bundled with this package in the file docs/LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://blipoteka.pl/license
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to blipoteka@gmail.com so we can send you a copy immediately.
 *
 * @category   Blipoteka
 * @package    Blipoteka_Service
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://blipoteka.pl/license Simplified BSD License
 */

/**
 * Book related service class
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Blipoteka_Service_Book extends Blipoteka_Service {
	/**
	 * Class of the record this service applies to
	 * @var string
	 */
	protected $_recordClass = 'Blipoteka_Book';

	/**
	 * The constructor
	 *
	 * @param Zend_Controller_Request_Abstract $request
	 * @param Void_Auth_Adapter_Interface $authAdapter
	 */
	public function __construct(Zend_Controller_Request_Abstract $request = null) {
		parent::__construct($request);
	}

	/**
	 * Return collection of books owned by user
	 *
	 * @param Blipoteka_User $user
	 * @return Doctrine_Collection
	 */
	public function getOwnedBookList(Blipoteka_User $user) {
		return $user->books_owned;
	}

	/**
	 * Return default query for selecting collection of books.
	 *
	 * @return Doctrine_Query
	 */
	public function getBookListQuery() {
		$query = Doctrine_Query::create();
		$query->select('book.book_id, book.type, book.status, book.title');
		$query->from($this->_recordClass . ' book');
		// Owner
		$query->leftJoin('book.owner owner');
		$query->addSelect('owner.blip');
		// Holder
		$query->leftJoin('book.holder holder');
		$query->addSelect('holder.blip');
		// Holder's city name
		$query->leftJoin('holder.city holder_city');
		$query->addSelect('holder_city.name');
		// Author
		$query->innerJoin('book.authors authors');
		$query->addSelect('authors.name');
		// Publisher
		$query->innerJoin('book.publisher publisher');
		$query->addSelect('publisher.name');
		// Sorting
		$query->orderBy('authors.name');
		$query->addOrderBy('book.title');
		return $query;
	}

	/**
	 * Get collection of all books.
	 * @return Doctrine_Collection
	 */
	public function getBookList() {
		$query = $this->getBookListQuery();
		$result = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		return $result;
	}

	/**
	 * Get paginator entity for collection of all books.
	 * @return Zend_Paginator
	 */
	public function getBookListPaginator() {
		$pageNumber = 0;
		$itemCountPerPage = 20;

		// Get book list query
		$query = $this->getBookListQuery();

		// Create an appropriate adapter
		$adapter = new Zend_Paginator_Adapter_Doctrine($query);
		$adapter->setHydrationMode(Doctrine_Core::HYDRATE_ARRAY);

		// Create paginator
		$paginator = new Zend_Paginator($adapter);
		$paginator->setCurrentPageNumber($pageNumber);
		$paginator->setItemCountPerPage($itemCountPerPage);

		return $paginator;
	}

}
