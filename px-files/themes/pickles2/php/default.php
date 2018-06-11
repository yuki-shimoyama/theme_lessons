<?php
/**
 * theme "pickles"
 */
namespace pickles2\themes\pickles;

/**
 * theme "pickles" class
 */
class theme_top{
	/**
	 * objects
	 */
	private $px, $theme;

	/**
	 * constructor
	 * @param object $px $px object
	 * @param object $theme $theme object
	 */
	public function __construct( $px, $theme ){
		$this->px = $px;
		$this->theme = $theme;
		// $this->px->path_theme_files('css/common.css');
		// $this->px->path_theme_files('css/layout.css');
		// $this->px->path_theme_files('css/modules_custom.css');
	}//__construct()

	/**
	 * グローバルメニューを自動生成する
	 *
	 * サイトマップCSVに登録されたページの一覧から、グローバルメニューを自動生成し、HTMLコードを返します。
	 *
	 * 対象となるページの一覧は、 `$px->site()->get_global_menu()` から取得します。
	 *
	 * @return string HTMLコード
	 */
	public function mk_global_menu(){
		$global_menu = $this->px->site()->get_global_menu();
		if( !count($global_menu) ){
			return '';
		}
		$rtn = '';
		$rtn .= '<ul class="header-menu-right">'."\n";
		foreach( $global_menu as $global_menu_page_id ){
			$rtn .= '<li class="menu-single">'.$this->px->mk_link( $global_menu_page_id );
			$rtn .= $this->mk_sub_menu( $global_menu_page_id );
			$rtn .= '</li>'."\n";
		}
		$rtn .= '</ul>'."\n";
		return $rtn;
	}

	/**
		 * 指定されたページの子階層のメニューを展開する
		 *
		 * 主にローカルナビゲーションを生成する用途を想定したメソッドです。 `$parent_page_id` に与えられたページを頂点として、ページの階層構造を HTML化して生成します。
		 * カレントページの直系の祖先にあたる階層は、子階層が開かれた状態で生成され、直系に当たらない階層は隠されます。
		 *
		 * @param string $parent_page_id 親ページのページID
		 * @return string ページリストのHTMLコード
		 */
		public function mk_sub_menu( $parent_page_id ){
			$rtn = '';
			$children = $this->px->site()->get_children( $parent_page_id );
			if( count($children) ){
				$rtn .= '<ul class="header-submenu-right">'."\n";
				foreach( $children as $child ){
					$rtn .= '<li class="menu-second">'.$this->px->mk_link( $child );
					if( $this->px->site()->is_page_in_breadcrumb( $child ) ){
						$rtn .= $this->mk_sub_menu( $child );//←再帰的呼び出し
					}
					$rtn .= '</li>'."\n";
				}
				$rtn .= '</ul>'."\n";
			}
			return $rtn;
		}

		/**
			 * パンくずを自動生成する
			 *
			 * このメソッドは、パンくずリストのHTMLコードを生成して返します。
			 * 祖先ページは aタグ で囲われ、カレントページは aタグの代わりに spanタグ で囲われます。
			 *
			 * @return string パンくずのHTMLコード
			 */
			public function mk_breadcrumb(){
				$breadcrumb = $this->px->site()->get_breadcrumb_array();
				$rtn = '';
				$rtn .= '<ul class="breadcrumbs-inner">';
				foreach( $breadcrumb as $pid ){
					$rtn .= '<li>';
					if( $this->px->href($pid) != $this->px->href($this->px->site()->get_current_page_info('id')) ){
						$rtn .= $this->px->mk_link( $pid, array('label'=>$this->px->site()->get_page_info($pid, 'title_breadcrumb'), 'current'=>false) );
					}else{
						$rtn .= '<span>'.htmlspecialchars( $this->px->site()->get_page_info($pid, 'title_breadcrumb') ).'</span>';
					}
					$rtn .= '</li>';
				}
				$rtn .= '<li><span>'.htmlspecialchars( $this->px->site()->get_current_page_info('title_breadcrumb') ).'</span></li>';
				$rtn .= '</ul>';
				return $rtn;
			}
}
