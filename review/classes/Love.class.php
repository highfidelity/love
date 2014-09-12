<?php

class Love
{
	protected $loveHistoryPageLimit;
	protected $limit;
	
	public $pages;
	
	public function setLimit($limit)
	{
		$this->limit = (int)$limit;
		return $this;
	}
	
	public function getLimit()
	{
		return $this->limit;
	}

	public function __construct(array $options = null)
	{
        if (is_array($options)) {
            $this->setOptions($options);
        }
        return $this;
	}

	// This function returns the
	// 10 users who were the receivers of
	// most love messages
	// If the current user is not among them
	// an 11th element is added to the end
	// showing the current position of the user
	public function getMostLoved()
	{
		$front = Frontend::getInstance();
		$sql =  "SELECT ".LOVE_USERS.".id, ".LOVE_USERS.".nickname, count(".LOVE_LOVE.".receiver)".
                "FROM ".LOVE_LOVE." ".
                "INNER JOIN ".LOVE_USERS." ". 
                "ON ".LOVE_LOVE.".receiver = ".LOVE_USERS.".username ". 
                "GROUP BY ".LOVE_LOVE.".receiver ".
                "ORDER BY count(".LOVE_LOVE.".receiver) desc";
		$res = mysql_query($sql);
		$pos = 1;
		$found = false;
		$output = "";
		while($row = mysql_fetch_assoc($res)){
			if($pos > 10 && $found === TRUE){
				break;
			}
			if($row["id"] == $front->getUser()->getId()){
				$output .= "<li class=\"me\">".$pos.") ".$row["nickname"]."</li>";
				$found = true;
			} else if($pos <= 10){
				$output .= "<li>".$pos.") ".$row["nickname"]."</li>";
			}
			$pos++;
		}
		return $output;
	}

	// This function gets all company love messages
	// that the current user is member of and displays
	// them in a table
	public function getLoveHistory($page, $justUser = false)
	{
		$front = Frontend::getInstance();
		$page--;
		$l = $this->getLimit() * $page;
		$where = '';
			
		$sql = "SELECT count(*) ".
               "FROM ".LOVE_LOVE;
		$res = mysql_query($sql);
		$row = mysql_fetch_row($res);
		$loves = $row[0];

		$sql = "SELECT count(*) ".
               "FROM ".LOVE_LOVE." ".
               "WHERE ".LOVE_LOVE.".receiver = '".$front->getUser()->getUsername()."' ".
               "OR ".LOVE_LOVE.".giver = '".$front->getUser()->getUsername()."' ".
       $sql .= $justUser ? '' : 
    	           "OR ".LOVE_LOVE.".company_id = '".$front->getUser()->getCompany_id()."' ";
   		$sql .= $where." ".
               "ORDER BY id DESC";
		$res = mysql_query($sql);
		$row = mysql_fetch_row($res);
		$count = $row[0];
		$cPages = ceil($count/$this->getLimit());

		$sql = "SELECT id,giver,receiver,why,private,TIMESTAMPDIFF(SECOND,at,NOW()) as delta ".
               "FROM ".LOVE_LOVE." ".
               "WHERE ".LOVE_LOVE.".receiver = '".$front->getUser()->getUsername()."' ".
               "OR ".LOVE_LOVE.".giver = '".$front->getUser()->getUsername()."' ";
        $sql .= $justUser ? '' : 
		           "OR ".LOVE_LOVE.".company_id = '".$front->getUser()->getCompany_id()."' ";
		$sql .= $where." ".
               "ORDER BY id DESC ".
               "LIMIT ".$l.",".$this->getLimit();
		$res = mysql_query($sql);

		// Construct json for history
		$this->pages = array(array($page, $cPages, number_format($loves)));
		for ($i = 1; $row=mysql_fetch_assoc($res); $i++){
			$givernickname = getNickName($row['giver']);
			$givernickname = (!empty($givernickname))?($givernickname):($row['giver']);

			$receivernickname = getNickName($row['receiver']);
			$receivernickname = (!empty($receivernickname))?($receivernickname):($row['receiver']);

			$why = $row['why'];
			if ($row['private']) $why .= " (love sent quietly)";

			$history[] = array(
            "id"               => $row['id'], 
            "giver"            => $row['giver'], 
            "giverNickname"    => $givernickname, 
            "receiver"         => $row['receiver'], 
            "receiverNickname" => $receivernickname, 
            "why"              => $why, 
            "delta"            => Utils::relativeTime($row['delta']));
		}
		return $history;
	}

	public function getLoveList($page, $justUser = false)
	{
		$els = $this->getLoveHistory($page, $justUser);
		$output = "";
		$i = 0;
		foreach($els as $el){
		    if($i == 1) {
		        $class = 'rowodd';
		        $i = 0;
		    } else {
		        $class = 'roweven';
		        $i = 1;
		    }
			$output .= "<tr class=\"$class\">";
			$output .= "<td class=\"headFrom\">";
			$output .= $el["giverNickname"];
			$output .= "</td>";
			$output .= "<td class=\"headTo\">";
			$output .= $el["receiverNickname"];
			$output .= "</td>";
			$output .= "<td class=\"headFor\">";
			$output .= htmlentities($el["why"]);
			$output .= "</td>";
			$output .= "<td class=\"headWhen\">";
			$output .= $el["delta"];
			$output .= "</td>";
			$output .= "</tr>";
		}
		return $output;
	}
	
    // generates the pager for love list table
    public function getListPager($page)
    {
        $output = '<ul id="pager">';
        if($page == 1){
            $output .= '<li id="page">1</li>';
            $output .= '<li id="next">></li>';
        } else if($page == $this->pages[0][1]){
            $output .= '<li id="prev"><</li>';
            $output .= '<li id="page">'.$this->pages[0][1].'</li>';
        } else {
            $output .= '<li id="prev"><</li>';
            $output .= '<li id="page">'.$page.'</li>';
            $output .= '<li id="next">></li>';
        }
        $output .= "</ul>";
        return $output;
    }
	
	/**
     * Automatically sets the options array
     * Array: Name => Value
     *
     * @param array $options
     * @return User $this
     */
	private function setOptions(array $options)
	{
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
	}

}
