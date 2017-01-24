<?php 
require_once(__DIR__.'/../config/config.php');
	

namespace Rank;

class AccessRanking {

	public $result_ur;  // user_rankingのカテゴリーの取得など
	public $resultTR;  // IN OUT の集計
	public $resultMR;  // IN OUT の集計
	public $resultMRCate;  // IN OUT の集計
	public $resultWR;  // IN OUT の集計
	public $resultWRR;  // IN OUT の集計
	private $in_out;
	public $return_cate;  //JSON型から戻した配列
	public $cate_c;  //登録カテゴリーの個数
	private $ip_int;  //数字にしたipアドレス
	public $result_c_cA;  //カテゴリーAのランキング
	public $result_c_cB;  //カテゴリーBのランキング
	// /public $result_c_cC;  //カテゴリーCのランキング
	public $rank_log;
	public $rank_logg;
	public $rank_log_cate;
	public $rank_logg_cate;
	public $totalRank_log;
	//public $totalRank_log_cate;
	public $file_time;  //failの更新日時
	public $week_ago;  //１週間前の時刻


	public function __construct() {
		$this->idCheck();
		$this->getIp();
		if (isset($_GET['si_id']) && $_GET['si_id'] != "") {
			$this->ipCheck();
		}

		$this->selectUR();

		// IN OUT サイト名 コメントの集計
		$this->selectTotalRank();
		//$this->selectTotalRankCate();

		$this->dayCheck();
		$this->selectTotalMonthRank();
		$this->selectTotalMonthRankCate();
		
	}

	// count_accessから値を取得。総合ランキングを表示
	private function selectTotalRank() {
		$db = getDb();
		$sql = "SELECT c_a.si_id, COUNT(c_a.in_out = 'in' OR NULL) AS c_in, COUNT(c_a.in_out = 'out' OR NULL) AS c_out, s_i.site_name, s_i.site_comment, s_i.site_url, s_i.site_img, s_i.site_url, s_i.e_category FROM count_access AS c_a INNER JOIN (SELECT * FROM site_info WHERE ur_site_id = :ur_site_id) AS s_i ON c_a.si_id = s_i.si_id GROUP BY c_a.si_id ORDER BY c_in DESC, c_out DESC, si_id DESC";
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':ur_site_id', $_GET['id'], \PDO::PARAM_STR);
		$stmt->execute();
		$this->resultTR = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		foreach ($this->resultTR as $a) {
			$totalRank_log[] = $a['si_id'];
		}
		$this->totalRank_log = $totalRank_log;
		
	}

	// 1ヶ月ごとに集計を取る。
	private function selectTotalMonthRank() {
		$db = getDb();
		$sql = "SELECT c_a.stamp_date, c_a.si_id, COUNT(c_a.in_out = 'in' OR NULL) AS c_in, COUNT(c_a.in_out = 'out' OR NULL) AS c_out, s_i.site_name, s_i.site_comment, s_i.site_url, s_i.site_img, s_i.site_url, s_i.e_category FROM count_access AS c_a INNER JOIN (SELECT * FROM site_info WHERE ur_site_id = :ur_site_id) AS s_i ON c_a.si_id = s_i.si_id WHERE c_a.stamp_date >= DATE_FORMAT(now(), '%Y-%m-01') GROUP BY s_i.si_id ORDER BY c_in DESC, c_out DESC, si_id DESC";
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':ur_site_id', $_GET['id'], \PDO::PARAM_STR);
		$stmt->execute();
		$this->resultMR = $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}
	// 1ヶ月ごとに集計を取る。カテゴリー別
	private function selectTotalMonthRankCate() {
		for ($i = 0; $i < $this->cate_c; $i++) {
			$db = getDb();
			$sql = "SELECT c_a.stamp_date, c_a.si_id, COUNT(c_a.in_out = 'in' OR NULL) AS c_in, COUNT(c_a.in_out = 'out' OR NULL) AS c_out, s_i.site_name, s_i.site_comment, s_i.site_url, s_i.site_img, s_i.site_url, s_i.e_category FROM count_access AS c_a INNER JOIN (SELECT * FROM site_info WHERE ur_site_id = :ur_site_id AND e_category = :e_category) AS s_i ON c_a.si_id = s_i.si_id WHERE c_a.stamp_date >= DATE_FORMAT(now(), '%Y-%m-01') GROUP BY s_i.si_id ORDER BY c_in DESC, c_out DESC, si_id DESC";
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':ur_site_id', $_GET['id'], \PDO::PARAM_STR);
			$stmt->bindValue(':e_category', $this->return_cate[$i], \PDO::PARAM_STR);
			$stmt->execute();
			$this->resultMRCate[$i] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		}
	}
///////////////////////////////////////////////////////////////////////////////////
	//先週間ランキング
///////////////////////////////////////////////////////////////////////////////////
	// 先週の順位を記録。比較してみて、前回の順位より上がったかを測定。AAAAAAAAAAAAAAA
	private function selectTotalWeekRankA() {
		$db = getDb();
		$sql = "SELECT c_a.stamp_date, c_a.si_id, COUNT(c_a.in_out = 'in' OR NULL) AS c_in, COUNT(c_a.in_out = 'out' OR NULL) AS c_out, s_i.site_name, s_i.site_comment, s_i.site_url, s_i.site_img, s_i.site_url, s_i.e_category FROM count_access AS c_a INNER JOIN (SELECT * FROM site_info WHERE ur_site_id = :ur_site_id) AS s_i ON c_a.si_id = s_i.si_id WHERE date > now() - INTERVAL 1 WEEK GROUP BY c_a.si_id ORDER BY c_in DESC, c_out DESC, si_id DESC";
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':ur_site_id', $_GET['id'], \PDO::PARAM_STR);
		$stmt->execute();
		$this->resultWR = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		foreach ($this->resultWR as $a) {
			$rank_log[] = $a['si_id'];
		}
		$this->rank_log = $rank_log;
	}
///////////////////////////////////////////////////////////////////////////////////
	//先々週間ランキング
///////////////////////////////////////////////////////////////////////////////////
	// 先週の順位を記録。比較してみて、前回の順位より上がったかを測定。AAAAAAAAAAAAAAA
	private function selectTotalWeekRankAA() {
		$db = getDb();
		$sql = "SELECT c_a.stamp_date, c_a.si_id, COUNT(c_a.in_out = 'in' OR NULL) AS c_in, COUNT(c_a.in_out = 'out' OR NULL) AS c_out, s_i.site_name, s_i.site_comment, s_i.site_url, s_i.site_img, s_i.site_url, s_i.e_category FROM count_access AS c_a INNER JOIN (SELECT * FROM site_info WHERE ur_site_id = :ur_site_id) AS s_i ON c_a.si_id = s_i.si_id WHERE date > now() - INTERVAL 2 WEEK GROUP BY c_a.si_id ORDER BY c_in DESC, c_out DESC, si_id DESC";
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':ur_site_id', $_GET['id'], \PDO::PARAM_STR);
		$stmt->execute();
		$this->resultWRR = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		foreach ($this->resultWRR as $a) {
			$rank_log[] = $a['si_id'];
		}
		$this->rank_logg = $rank_log;
	}
///////////////////////////////////////////////////////////////////////////////////
	//先週間ランキング カテゴリー別
///////////////////////////////////////////////////////////////////////////////////
	// 先週の順位を記録。比較してみて、前回の順位より上がったかを測定。AAAAAAAAAAAAAAA
	private function selectTotalWeekRankACate() {
		for ($i = 0; $i < $this->cate_c; $i++) {
			$db = getDb();
			$sql = "SELECT c_a.stamp_date, c_a.si_id, COUNT(c_a.in_out = 'in' OR NULL) AS c_in, COUNT(c_a.in_out = 'out' OR NULL) AS c_out, s_i.site_name, s_i.site_comment, s_i.site_url, s_i.site_img, s_i.site_url, s_i.e_category FROM count_access AS c_a INNER JOIN (SELECT * FROM site_info WHERE ur_site_id = :ur_site_id AND e_category = :e_category) AS s_i ON c_a.si_id = s_i.si_id WHERE date > now() - INTERVAL 1 WEEK GROUP BY c_a.si_id ORDER BY c_in DESC, c_out DESC, si_id DESC";
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':ur_site_id', $_GET['id'], \PDO::PARAM_STR);
			$stmt->bindValue(':e_category', $this->return_cate[$i], \PDO::PARAM_STR);
			$stmt->execute();
			$this->result_c_cA[$i] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			foreach ($this->result_c_cA[$i] as $a) {
				$rank_log[] = $a['si_id'];
			}
			$this->rank_log_cate[$i] = $rank_log;
		}
	}
///////////////////////////////////////////////////////////////////////////////////
	//先々週間ランキング カテゴリー別
///////////////////////////////////////////////////////////////////////////////////
	// 先週の順位を記録。比較してみて、前回の順位より上がったかを測定。AAAAAAAAAAAAAAA
	private function selectTotalWeekRankAACateCate() {
		for ($i = 0; $i < $this->cate_c; $i++) {
			$db = getDb();
			$sql = "SELECT c_a.stamp_date, c_a.si_id, COUNT(c_a.in_out = 'in' OR NULL) AS c_in, COUNT(c_a.in_out = 'out' OR NULL) AS c_out, s_i.site_name, s_i.site_comment, s_i.site_url, s_i.site_img, s_i.site_url, s_i.e_category FROM count_access AS c_a INNER JOIN (SELECT * FROM site_info WHERE ur_site_id = :ur_site_id AND e_category = :e_category) AS s_i ON c_a.si_id = s_i.si_id WHERE date > now() - INTERVAL 2 WEEK GROUP BY c_a.si_id ORDER BY c_in DESC, c_out DESC, si_id DESC";
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':ur_site_id', $_GET['id'], \PDO::PARAM_STR);
			$stmt->bindValue(':e_category', $this->return_cate[$i], \PDO::PARAM_STR);
			$stmt->execute();
			$this->result_c_cB[$i] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			foreach ($this->result_c_cB[$i] as $a) {
				$rank_log[] = $a['si_id'];
			}
			$this->rank_logg_cate[$i] = $rank_log;
		}
	}

	// count_accessから値を取得。カテゴリー別にランキングを表示
	private function selectCateRank() {
		for ($i = 0; $i < $this->cate_c; $i++) {
			$db = getDb();
			$sql = "SELECT c_a.stamp_date, c_a.si_id, COUNT(c_a.in_out = 'in' OR NULL) AS c_in, COUNT(c_a.in_out = 'out' OR NULL) AS c_out, s_i.site_name, s_i.site_comment, s_i.site_url, s_i.site_img, s_i.site_url, s_i.e_category FROM count_access AS c_a INNER JOIN (SELECT * FROM site_info WHERE ur_site_id = :ur_site_id AND e_category = :e_category) AS s_i ON c_a.si_id = s_i.si_id GROUP BY c_a.si_id ORDER BY c_in DESC, c_out DESC";
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':ur_site_id', $_GET['id'], \PDO::PARAM_STR);
			$stmt->bindValue(':e_category', $this->return_cate[$i], \PDO::PARAM_STR);
			$stmt->execute();
			$this->result_c_cA[$i] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		}
	}

	// ファイルに保存されたIDと一致するIDを照らし合わせ、保存されたIDが何番目の配列かをサーチする。
	public function rankSearchWeekAgo($site_id) {
		//array_searchは結果をintで返す。
		$index_id = array_search($site_id, $this->rank_log);
		// array_searchが失敗するとfalseを返す。返り値が0だとfalseと誤解されるため、条件式では===を使う。
		if ($index_id === FALSE) {
			$index_id = -1;
		}
		return $index_id;
	}
	public function rankSearchTwoWeekAgo($site_id) {
		//array_searchは結果をintで返す。
		$index_id = array_search($site_id, $this->rank_logg);
		// array_searchが失敗するとfalseを返す。返り値が0だとfalseと誤解されるため、条件式では===を使う。
		if ($index_id === FALSE) {
			$index_id = -1;
		}
		return $index_id;
	}

	// カテゴリー・・・ファイルに保存されたIDと一致するIDを照らし合わせ、保存されたIDが何番目の配列かをサーチする。 
	public function rankSearchWeekAgoCate($site_id, $i) {
		//array_searchは結果をintで返す。
		$index_id = array_search($site_id, $this->rank_log_cate[$i]);
		// array_searchが失敗するとfalseを返す。返り値が0だとfalseと誤解されるため、条件式では===を使う。
		if ($index_id === FALSE) {
			$index_id = -1;
		}
		return $index_id;
	}

	// カテゴリー・・・ファイルに保存されたIDと一致するIDを照らし合わせ、保存されたIDが何番目の配列かをサーチする。
	public function rankSearchTwoWeekAgoCate($site_id, $i) {
		//array_searchは結果をintで返す。
		$index_id = array_search($site_id, $this->rank_logg_cate[$i]);
		// array_searchが失敗するとfalseを返す。返り値が0だとfalseと誤解されるため、条件式では===を使う。
		if ($index_id === FALSE) {
			$index_id = -1;
		}
		return $index_id;
	}


	// トータル・・・ファイルに保存されたIDと一致するIDを照らし合わせ、保存されたIDが何番目の配列かをサーチする。
	public function TotalRankSearch($site_id) {
		//array_searchは結果をintで返す。
		$index_id = array_search($site_id, $this->totalRank_log);
		// array_searchが失敗するとfalseを返す。返り値が0だとfalseと誤解されるため、条件式では===を使う。
		if ($index_id === FALSE) {
			$index_id = -1;
		}
		return $index_id;
	}

}

