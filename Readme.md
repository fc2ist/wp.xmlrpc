#Wordpress XML-RPC Class
WordpressのXML-RPC投稿を簡単に行うためのクラスです。

##Usage
  require_once('wp.xmlrpc.php');
  
  $host     = 'hogehoge.net';
  $user     = 'admin';
  $password = 'mypassword';
  $endpoint = 'http://hogehoge.net/xmlrpc.php';
  
  $wp = new wpXMLRPC($endpoint, $host, $user, $password);
  
  $data = array(
    'post_title' => 'タイトル',
    'post_content' => '内容です！',
    'post_thumbnail' => 'test.jpg',
    'terms_names' => array(
      'post_tag' => array('タグ１', 'タグ２')
    )
  );
  
  $wp->newPost($data)

##Public Method

  /**
   * コンストラクタ
   *
   * @param    string  $path        xmlrpc.phpのURL
   * @param    string  $host        ホスト名
   * @param    string  $user        Wordpressユーザー名
   * @param    string  $password    Wordpressパスワード
   * @return   void
   */

  /**
   * 新規記事投稿
   *
   * @param    struct  $data    投稿内容のデータ
   * @return   struct
   */

  /**
   * Term一覧取得
   *
   * @param    string  $taxonomy    タクソノミー
   * @param    struct  $filter      Term ID
   * @return   struct
   */

  /**
   * Term作成
   *
   * @param    struct  $content    Termデータ
   * @return   struct
   */

  /**
   * ファイルアップロード
   *
   * @param    string  $path    ファイルパス
   * @return   struct
   */

##Author
Twitter: @moi_fc2  
Blog: http://fc2ist.blog.fc2.com/

##License
Copyright &copy; 2012 @moi_fc2.
Licensed under the [MIT License](http://www.opensource.org/licenses/mit-license.php).