<?php

namespace AppBundle\Repository;

use DateTime;
use \Doctrine\DBAL\Types\Type;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends \Doctrine\ORM\EntityRepository
{

	public function getOwnerId($post_id) {
		$query = "SELECT user_id, origin_user_id FROM post WHERE id = ?";
		$params = array($post_id);

		$sth = $this->getEntityManager()->getConnection()->prepare($query);
		$sth->execute($params);
		$row = $sth->fetch();

		if($row['origin_user_id'] != null) {
			return $row['origin_user_id'];
		}

		return $row['user_id'];
	}

	public function listVoted($post_id) {
		$query = "SELECT u.id, u.name FROM post_votes v INNER JOIN user u ON u.id = v.user_id WHERE v.post_id = ?";
		$params = array($post_id);

		$sth = $this->getEntityManager()->getConnection()->prepare($query);
		$sth->execute($params);
		return $sth->fetchAll();
	}

	public function numVotes($post_id) {
		$query = "SELECT COUNT(*) AS CNT FROM post_votes WHERE post_id = ?";
		$params = array($post_id);

		$sth = $this->getEntityManager()->getConnection()->prepare($query);
		$sth->execute($params);
		$row = $sth->fetch();

		return $row['CNT'];
	}

	public function isHidden($user_id, $post_id) {
		$query = "SELECT COUNT(*) AS CNT FROM post_hidden WHERE user_id = ? AND post_id = ?";
		$params = array($user_id, $post_id);

		$sth = $this->getEntityManager()->getConnection()->prepare($query);
		$sth->execute($params);
		$row = $sth->fetch();

		return $row['CNT'] >= 1;
	}

	public function hidePost($user_id, $post_id) {
		$query = "INSERT INTO post_hidden (user_id, post_id) VALUES (?, ?)";
		$params = array($user_id, $post_id);

		$sth = $this->getEntityManager()->getConnection()->prepare($query);
		$sth->execute($params);
	}

	public function isPinned($user_id, $post_id) {
		$query = "SELECT COUNT(*) AS CNT FROM post WHERE user_id = ? AND id = ? and is_pinned = true AND pinned_at < DATE_ADD(NOW(), INTERVAL 7 DAY)";
		$params = array($user_id, $post_id);

		$sth = $this->getEntityManager()->getConnection()->prepare($query);
		$sth->execute($params);
		$row = $sth->fetch();

		return $row['CNT'] >= 1;
	}

	public function unpinPost($user_id, $post_id) {
		$query = "UPDATE post SET is_pinned = false, pinned_at = null WHERE user_id = ? AND id = ?";
		$params = array($user_id, $post_id);

		$sth = $this->getEntityManager()->getConnection()->prepare($query);
		$sth->execute($params);
	}

	public function getTextOfPost($post_id) {
		$sth = $this->getEntityManager()->getConnection()->prepare("SET @@character_set_client=utf8mb4, @@character_set_connection=utf8mb4");
		$sth->execute();
		$query = "SELECT p.text FROM post p WHERE p.id = ?";
		$params = array($post_id);

		$sth = $this->getEntityManager()->getConnection()->prepare($query);
		$sth->execute($params);
		return $sth->fetchAll();
	}

	public function configureConnection() {
		$sth = $this->getEntityManager()->getConnection()->prepare("SET @@character_set_client=utf8mb4, @@character_set_connection=utf8mb4");
		$sth->execute();
		$sth = $this->getEntityManager()->getConnection()->prepare("SET NAMES utf8mb4");
		$sth->execute();
	}

	public function updateText($post_id, $text) {
		$this->configureConnection();

		$query = "UPDATE post SET text = ? WHERE id = ?";
		$params = array($text, $post_id);

		$sth = $this->getEntityManager()->getConnection()->prepare($query);
		$sth->execute($params);
	}

	public function pinPost($user_id, $post_id) {
		$query = "UPDATE post SET is_pinned = true, pinned_at = NOW() WHERE user_id = ? AND id = ?";
		$params = array($user_id, $post_id);

		$sth = $this->getEntityManager()->getConnection()->prepare($query);
		$sth->execute($params);
	}

	public function hasVote($user_id, $post_id) {
		$query = "SELECT COUNT(*) AS CNT FROM post_votes WHERE user_id = ? AND post_id = ?";
		$params = array($user_id, $post_id);

		$sth = $this->getEntityManager()->getConnection()->prepare($query);
		$sth->execute($params);
		$row = $sth->fetch();

		return $row['CNT'] >= 1;
	}

	public function addVote($user_id, $post_id) {
		$query = "INSERT INTO post_votes (user_id, post_id) VALUES (?, ?)";
		$params = array($user_id, $post_id);

		$sth = $this->getEntityManager()->getConnection()->prepare($query);
		$sth->execute($params);
	}

    public function findByTradelands($ids, $return_comments = false, $from_date = null)
    {
        // Here we retrieve pinned posts added by user
  		$in = $this->createQueryBuilder('p2');
        $in->innerJoin("p2.post_hidden", "h", "WITH", "h.id = :me");

        $qb1 = $this->createQueryBuilder('p');
        $qb1->where('p.tradeland in :id')
            ->andWhere('p.pinnedAt <= :pinnedTime')
            ->andWhere('p.user != :me')
            ->andWhere('p.originUser != :me')
            ->andWhere('p.createdAt < :now')
            ->andWhere($qb1->expr()->notIn('p.id', $in->getDQL()))
//          ->addSelect('c')
//          ->leftJoin('p.comments', 'c')
//          ->addSelect('u')
//          ->leftJoin('c.user', 'u')
            ->setMaxResults(10)
            ->orderBy('p.createdAt', 'DESC')
        	->setParameter('me', \App::getInstance()->getUserId())
            ->setParameter('id', $ids)
            ->setParameter('pinnedTime', new DateTime('7 day'), Type::DATETIME)
            ->setParameter('now', new DateTime(), Type::DATETIME);

//      if (!is_null($from_date)) {
//          $qb1->andWhere('p.createdAt < :date')
//              ->setParameter('date', DateTime::createFromFormat('U', $from_date), Type::DATETIME);
//      }

        $records = $qb1->getQuery()->getResult();

        $posts = array();

		// Here the list of ids of pinned posts
        foreach($records as &$post) {
        	$posts[] = $post->getId();
        }

        // Here we retrieve all posts except pinned
        $qb = $this->createQueryBuilder('p');
        $qb->where('p.tradeland in :id')
            ->andWhere('p.createdAt < :now')
            ->andWhere('p.user != :me')
            ->andWhere('p.originUser != :me')
            ->andWhere($qb->expr()->notIn('p.id', $in->getDQL()))
       		->setParameter('me', \App::getInstance()->getUserId())
            ->setParameter('now', new DateTime(), Type::DATETIME);

        if(count($posts) > 0) {
        	 $qb->andWhere('p.id not in (:posts)')
                ->setParameter('posts', $posts);
        }
 
        if (!is_null($from_date)) {
            $qb->andWhere('p.createdAt < :date')
                ->setParameter('date', DateTime::createFromFormat('U', $from_date), Type::DATETIME);
        }

        $qb->setParameter('id', $ids)
//          ->addSelect('c')
//          ->leftJoin('p.comments', 'c')
//          ->addSelect('u')
//          ->leftJoin('c.user', 'u')
            ->setMaxResults(10)
            ->orderBy('p.createdAt', 'DESC');

        if (!is_null($from_date)) {
        	// we don't include pinned posts if from date exists
       		return $qb->getQuery()->getResult();
        }

        return array_merge($records, $qb->getQuery()->getResult());
    }

    public function findByUserAndConnections($ids, $from_date = null)
    {
        // Here we retrieve pinned posts added by user
  		$in = $this->createQueryBuilder('p2');
        $in->innerJoin("p2.post_hidden", "h", "WITH", "h.id = :me");

        $qb1 = $this->createQueryBuilder('p');
        $qb1->where('p.user = :id')
            ->andWhere('p.tradeland is NULL')
            ->andWhere('p.pinnedAt <= :pinnedTime')
            ->andWhere('p.createdAt < :now')
            ->andWhere($qb1->expr()->notIn('p.id', $in->getDQL()))
            ->addSelect('c')
            ->leftJoin('p.comments', 'c')
            ->addSelect('u')
            ->leftJoin('c.user', 'u')
            ->setMaxResults(10)
            ->orderBy('p.createdAt', 'DESC')
        	->setParameter('me', \App::getInstance()->getUserId())
            ->setParameter('id', $ids[0])
            ->setParameter('pinnedTime', new DateTime('7 day'), Type::DATETIME)
            ->setParameter('now', new DateTime(), Type::DATETIME);

//      if (!is_null($from_date)) {
//          $qb1->andWhere('p.createdAt < :date')
//              ->setParameter('date', DateTime::createFromFormat('U', $from_date), Type::DATETIME);
//      }

        $records = $qb1->getQuery()->getResult();

        $posts = array();

		// Here the list of ids of pinned posts
        foreach($records as &$post) {
        	$posts[] = $post->getId();
        }

        // Here we retrieve all posts except pinned
        $qb = $this->createQueryBuilder('p');
        $qb->where('p.user in (:id)')
            ->andWhere('p.tradeland is NULL')
            ->andWhere('p.createdAt < :now')
            ->andWhere($qb->expr()->notIn('p.id', $in->getDQL()))
       		->setParameter('me', \App::getInstance()->getUserId())
            ->setParameter('now', new DateTime(), Type::DATETIME);

        if(count($posts) > 0) {
        	 $qb->andWhere('p.id not in (:posts)')
                ->setParameter('posts', $posts);
        }
 
        if (!is_null($from_date)) {
            $qb->andWhere('p.createdAt < :date')
                ->setParameter('date', DateTime::createFromFormat('U', $from_date), Type::DATETIME);
        }

        $qb->setParameter('id', $ids)
            ->addSelect('c')
            ->leftJoin('p.comments', 'c')
            ->addSelect('u')
            ->leftJoin('c.user', 'u')
            ->setMaxResults(10)
            ->orderBy('p.createdAt', 'DESC');

        if (!is_null($from_date)) {
        	// we don't include pinned posts if from date exists
       		return $qb->getQuery()->getResult();
        }

        return array_merge($records, $qb->getQuery()->getResult());
    }
}
