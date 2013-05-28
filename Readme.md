#Wordpress XML-RPC Class
WordpressのXML-RPC投稿を簡単に行うためのクラスです。

##Usage
このサンプルでいう`$data`が[wp.newPost - XML-RPC WordPress API/Posts](http://codex.wordpress.org/XML-RPC_WordPress_API/Posts#wp.newPost)の`struct content`にあたる変数になっています。
~~~~~~{.usage}
require_once('wp.xmlrpc.php');

$host     = 'hogehoge.net';
$user     = 'admin';
$password = 'mypassword';
$endpoint = 'http://hogehoge.net/xmlrpc.php';

$wp = new wpXMLRPC($endpoint, $host, $user, $password);

$data = array(
  'post_title' => 'タイトル',
  'post_content' => '内容です！',
  'post_thumbnail' => 'test.jpg', // 他ドメインも可
  'terms_names' => array(
    'post_tag' => array('タグ１', 'タグ２', array('name'=> 'タグ3', 'slug' => 'Tag3'))
  )
);

$wp->newPost($data)
~~~~~~

<ins>2013-05-29 :</ins> wp.newPost APIのcontentに独自に`terms_slugs`を追加しました。  
termのnameは一意でないため`terms_names`は避けて、`terms`かこちらの`terms_slugs`を使用することをオススメします。

~~~~~~
$data = array(
  'post_title' => 'タイトル',
  'post_content' => '内容です！',
  'terms_slugs' => array(
    'category' => array(
    	// 既存のCat1というスラッグを持つカテゴリーを指定
    	'Cat1',

    	// スラッグCat1-1が存在しなければ作成(このときnameが必須)、
	// 存在すればスラッグCat1-1を持つカテゴリが指定される
	array(
		'name' => 'カテゴリ1-1',
		'slug' => 'Cat1-1',
		'parent' => '0' // 親カテゴリのterm_id
	)
    )
  )
);
~~~~~~

##Public Method
~~~~~~{.method}
/**
 * コンストラクタ
 *
 * @param    string  $path        xmlrpc.phpのURL
 * @param    string  $host        ホスト名
 * @param    string  $user        Wordpressユーザー名
 * @param    string  $password    Wordpressパスワード
 * @return   void
 */
public function __construct(string, string, string, string);

/**
 * 新規記事投稿
 *
 * @param    struct  $data    投稿内容のデータ
 * @return   struct
 */
public function newPost(struct);

/**
 * Term一覧取得
 *
 * @param    string  $taxonomy    タクソノミー
 * @param    struct  $filter      Term ID
 * @return   struct
 */
public function getTerms(string, struct);

/**
 * Term作成
 *
 * @param    struct  $content    Termデータ
 * @return   struct
 */
public function newTerm(struct);

/**
 * ファイルアップロード
 *
 * @param    string  $path    ファイルパス
 * @param    string  $name    ファイル名
 * @return   struct
 */
 public function uploadFile(string, string);
~~~~~~

##Author
Twitter: [@moi_fc2](https://twitter.com/moi_fc2)  
Blog: [FC2.blog.hack();](http://fc2ist.blog.fc2.com/)

##License
Copyright &copy; 2012 @moi_fc2.
Licensed under the [MIT License](http://www.opensource.org/licenses/mit-license.php).