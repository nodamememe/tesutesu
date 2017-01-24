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
	afhaidshfiusadfffffffffffffffffffff

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
		sadfasdfdsafdsafdsafa

	// カテゴリー・・・ファイルに保存されたIDと一致するIDを照らし合わせ、保存されたIDが何番目の配列かをサーチする。
	public function rankSearchTwoWeekAgoCate($site_id, $i) {
		safadsfafdadsfas
	}


	// トータル・・・ファイルに保存されたIDと一致するIDを照らし合わせ、保存されたIDが何番目の配列かをサーチする。
	public function TotalRankSearch($site_id) {
		dfnkjsafidsifhsdajikfidsaufkjdsafuiasuddddd
	}

}

