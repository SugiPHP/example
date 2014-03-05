<?php

namespace Model;

use PDO;

class Links extends Common
{
	public function getLink($id)
	{
		$sql = "SELECT * FROM links WHERE id = :id";
		$sth = $this->db->prepare($sql);
		$sth->bindValue(":id", (int) $id, PDO::PARAM_INT);
		$sth->execute();

		return $sth->fetch(PDO::FETCH_ASSOC);
	}
}
