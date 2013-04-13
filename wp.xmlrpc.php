<?php
/**
 * Wordpress XML-RPC Class
 *
 * XML-RPCによる通信を簡単に行うためのラッパークラスです。
 *
 * PHP versions 5.3+
 * Wordpress version 3.4+
 *
 * @category   Web Services
 * @author     moi <twitter: @moi_fc2>
 * @copyright  2012 moi
 * @license    MIT Licence
 * @version    v1.0.0
 * @link       http://url.com
 */

require_once('XML/RPC.php');

class wpXMLRPC {
  protected $client, $appkey, $user, $password, $blog_id;

  /**
   * コンストラクタ
   *
   * @param    string  $path        xmlrpc.phpのURL
   * @param    string  $host        ホスト名
   * @param    string  $user        Wordpressユーザー名
   * @param    string  $password    Wordpressパスワード
   * @return   void
   */
  public function __construct($path, $host, $user, $password) {
    $this->client   = new XML_RPC_client($path, $host, 80);
    $this->appkey   = new XML_RPC_Value('', 'string');
    $this->user     = new XML_RPC_Value($user, 'string');
    $this->password = new XML_RPC_Value($password, 'string');
    $this->blog_id  = new XML_RPC_Value(1, 'int');
  }

  /**
   * XML-RPC送信
   *
   * @param    struct  $message    XML_RPC_Message
   * @return   struct
   */
  protected function sendXMLRPC($message) {
    if ( !($res = $this->client->send($message)) ) {
      return array(
        'error' => 'サーバーに接続できません。'
      );
    } else if ( $res->faultCode() ) {
      return array(
        'error' => $res->faultString()
      );
    }
    return XML_RPC_decode($res->value());
  }

  /**
   * 新規記事投稿
   *
   * @param    struct  $data    投稿内容のデータ
   * @return   struct
   */
  public function newPost(array $data) {
    $content = array();

    // 投稿タイプ (post|page|attachment)
    if ( $post_type = $data['post_type'] ) {
      $content['post_type'] = new XML_RPC_Value($post_type, 'string');
    }
    // 公開ステータス (publish|pending|draft|private|static|object|attachment|inherit|future)
    if ( $post_status = $data['post_status'] ) {
      $content['post_status'] = new XML_RPC_Value($post_status, 'string');
    }
    // タイトル
    if ( $post_title = $data['post_title'] ) {
      $content['post_title'] = new XML_RPC_Value($post_title, 'string');
    } else {
      return array(
        'error' => 'タイトル(post_title)が設定されていません。'
      );
    }
    // 筆者
    if ( $post_author = $data['post_author'] ) {
      $content['post_author'] = new XML_RPC_Value($post_author, 'int');
    }
    // 概要
    if ( $post_excerpt = $data['post_excerpt'] ) {
      $content['post_excerpt'] = new XML_RPC_Value($post_excerpt, 'string');
    }
    // 内容
    if ( $post_content = $data['post_content'] ) {
      $content['post_content'] = new XML_RPC_Value($post_content, 'string');
    }
    if ( !$post_content && !$post_excerpt ) {
      return array(
        'error' => '内容(post_content)も概要(post_excerpt)も設定されていません。'
      );
    }
    // 投稿時間
    if ( $post_date_gmt = $data['post_date_gmt'] ) {
      $content['post_date_gmt'] = new XML_RPC_Value($post_date_dmt, 'dateTime.iso8601');
    } else if ( $post_date = $data['post_date'] ) {
      $content['post_date'] = new XML_RPC_Value($post_date, 'dateTime.iso8601');
    } else {
      $content['post_date'] = new XML_RPC_Value(time(), 'dateTime.iso8601');
    }
    // 投稿形式
    if ( $post_format = $data['post_format'] ) {
      $content['post_format'] = new XML_RPC_Value($post_format, 'string');
    }
    // 記事パスワード
    if ( $post_password = $data['post_password'] ) {
      $content['post_password'] = new XML_RPC_Value($post_password, 'string');
    }
    // コメント状態 (open|closed|registered_only)
    if ( $comment_status = $data['comment_status'] ) {
      $content['comment_status'] = new XML_RPC_Value($comment_status, 'string');
    }
    // トラックバック状態 (open|closed)
    if ( $ping_status = $data['ping_status'] ) {
      $content['ping_status'] = new XML_RPC_Value($ping_status, 'string');
    }
    // Sticky
    if ( $sticky = $data['sticky'] ) {
      $content['sticky'] = new XML_RPC_Value($sticky, 'bool');
    }
    // アイキャッチ画像
    if ( $post_thumbnail = $data['post_thumbnail'] ) {
      if ( is_numeric($post_thumbnail) ) {
        $res = $post_thumbnail;
      } else if ( is_array($post_thumbnail) ) {
        $res = $this->uploadFile($post_thumbnail['path'], $post_thumbnail['name']);
      } else {
        $res = $this->uploadFile($post_thumbnail);
      }
      if ( $res ) {
        $content['post_thumbnail'] = new XML_RPC_Value($res['id'], 'int');
      } else {
        return array(
          'error' => 'アイキャッチ画像のアップロードに失敗しました。'
        );
      }
    }
    // 親記事
    if ( $post_parent = $data['post_parent'] ) {
      $content['post_parent'] = new XML_RPC_Value($post_parent, 'int');
    }
    // カスタムフィールド
    if ( $custom_fields = $data['custom_fields'] ) {
      $content['custom_fields'] = array();
      foreach($custom_fields as $field) {
        $content['custom_fields'][] = new XML_RPC_Value(
          array(
            'key' => new XML_RPC_Value($field['key'], 'string'),
            'value' => new XML_RPC_Value($field['value'], 'string')
          ),
          'struct'
        );
      }
      $content['custom_fields'] = new XML_RPC_Value($content['custom_fields'], 'struct');
    }
    // スラッグ名
    if ( $post_name = $data['post_name'] ) {
      $content['post_name'] = new XML_RPC_Value($post_name, 'string');
    }
    // terms
    if ( $terms = $data['terms'] ) {
      $content['terms'] = array();
      foreach($terms as $key => $value) {
        $content['terms'][$key] = array();
        foreach($value as $id) {
          $content['terms'][$key][] = new XML_RPC_Value($id, 'int');
        }
        $content['terms'][$key] = new XML_RPC_Value($content['terms'][$key], 'struct');
      }
      $content['terms'] = new XML_RPC_Value($content['terms'], 'struct');
    }
    // terms_names
    if ( $terms_names = $data['terms_names'] ) {
      $content['terms_names'] = array();
      foreach($terms_names as $key => $value) {
        $termList = $this->createHash( $this->getTerms($key), 'name' );
        $content['terms_names'][$key] = array();
        foreach($value as $name) {
          if (!$termList[$name]) {// Termが存在しない場合は作成
            $this->newTerm(
              array(
                'name' => $name,
                'taxonomy' => $key
              )
            );
          }
          $content['terms_names'][$key][] = new XML_RPC_Value($name, 'string');
        }
        $content['terms_names'][$key] = new XML_RPC_Value($content['terms_names'][$key], 'struct');
      }
      $content['terms_names'] = new XML_RPC_Value($content['terms_names'], 'struct');
    }
    // enclosure
    if ( $enclosure = $data['enclosure'] ) {
      $content['enclosure'] = new XML_RPC_Value(
        array(
          'url' => new XML_RPC_Value($enclosure['url'], 'string'),
          'length' => new XML_RPC_Value($enclosure['length'], 'int'),
          'type' => new XML_RPC_Value($enclosure['type'], 'string')
        ),
        'struct'
      );
    }
    $content = new XML_RPC_Value($content, 'struct');
    $publish = new XML_RPC_Value(1, 'boolean');
    $message = new XML_RPC_Message(
      'wp.newPost',
      array($this->blog_id, $this->user, $this->password, $content, $publish)
    );
    return $this->sendXMLRPC($message);
  }

  /**
   * Term一覧取得
   *
   * @param    string  $taxonomy    タクソノミー
   * @param    struct  $filter      Term ID
   * @return   struct
   */
  public function getTerms($taxonomy = null, array $filter = array()) {
    $data = array(
      $this->blog_id,
      $this->user,
      $this->password
    );
    if ( $taxonomy === null ) {
      return array(
        'error' => 'タクソノミーを指定してください。'
      );
    }

    $data[3] = new XML_RPC_Value($taxonomy, 'string');
    if ( count($filter) ) {
      $filterData = array();
      if ( $number = $filter['number'] ) {
        $filterData['number'] = new XML_RPC_Value($number, 'int');
      }
      if ( $offset = $filter['offset'] ) {
        $filterData['offset'] = new XML_RPC_Value($offset, 'int');
      }
      if ( $orderby = $filter['orderby'] ) {
        $filterData['orderby'] = new XML_RPC_Value($orderby, 'string');
      }
      if ( $order = $filter['order'] ) {
        $filterData['order'] = new XML_RPC_Value($order, 'string');
      }
      if ( $hide_empty = $filter['hide_empty'] ) {
        $filterData['hide_empty'] = new XML_RPC_Value($hide_empty, 'bool');
      }
      if ( $search = $filter['search'] ) {
        $filterData['search'] = new XML_RPC_Value($search, 'string');
      }
      $data[4] = new XML_RPC_Value($filterData, 'struct');
    }
    $message = new XML_RPC_Message(
      'wp.getTerms', $data
    );
    return $this->sendXMLRPC($message);
  }

  /**
   * Term作成
   *
   * @param    struct  $content    Termデータ
   * @return   struct
   */
  public function newTerm(array $content) {
    $data = array(
      $this->blog_id,
      $this->user,
      $this->password
    );
    $contentData = array(
      'name' => new XML_RPC_Value($content['name'], 'string'),
      'taxonomy' => new XML_RPC_Value($content['taxonomy'], 'string')
    );
    if ( $slug = $content['slug'] ) {
      $contentData['slug'] = new XML_RPC_Value($slug, 'string');
    }
    if ( $description = $content['description'] ) {
      $contentData['description'] = new XML_RPC_Value($description, 'string');
    }
    if ( $parent = $content['parent'] ) {
      $contentData['parent'] = new XML_RPC_Value($parent, 'int');
    }
    $data[3] = new XML_RPC_Value($contentData, 'struct');
    $message = new XML_RPC_Message(
      'wp.newTerm', $data
    );
    return $this->sendXMLRPC($message);
  }


  /**
   * ファイルアップロード
   *
   * @param    string  $path    ファイルパス
   * @param    string  $name    ファイル名
   * @return   struct
   */
  public function uploadFile($path, $name = null) {
    $data = file_get_contents($path);
    $info = new FInfo(FILEINFO_MIME_TYPE);
    $mimeType = $info->buffer($data);
    $fileName = $name ? $name : basename($path);
    $file = new XML_RPC_Value(
      array(
        'type' => new XML_RPC_Value($mimeType, 'string'),
        'bits' => new XML_RPC_Value($data, 'base64'),
        'name' => new XML_RPC_Value($fileName, 'string')
      ),
      'struct'
    );
    $message = new XML_RPC_Message(
      'wp.uploadFile',
      array($this->blog_id, $this->user, $this->password, $file)
    );
    return $this->sendXMLRPC($message);
  }

  /**
   * ハッシュ生成
   *
   * @param    struct  $terms    Term一覧
   * @param    string  $key      ファイルパス
   * @return   struct
   */
  protected function createHash(array $terms, $key) {
    $hash = array();
    foreach($terms as $term) {
      $hash[ $term[$key] ] = $term;
    }
    return $hash;
  }
}

?>