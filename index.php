<?php

ini_set('log_errors','on');  //ログを取るか
ini_set('error_log','php.log');  //ログの出力ファイルを指定
session_start(); //セッション使う


$debug_flg = true;
//デバッグログ関数
function debug($str){
  global $debug_flg;
  if(!empty($debug_flg)){
    error_log('デバッグ：'.$str);
  }
}

//　プレイヤーカテゴリー
class Playertype{
  const BRAVE = 1;
  const WIZARD = 2;
}
// モンスターカテゴリー
class Monstercategory{
  const BASIC = 1;
  const MAGIC = 2;
  const FLY = 3;
  const BOSS =4;
}
// 性別クラス
class Sex{
  const MAN = 1;
  const WOMAN = 2;
  const OKAMA = 3;
}

// 抽象クラス（生き物クラス）
abstract class Creature{
  protected $name;
  protected $maxhp;
  protected $hp;
  protected $attackMin;
  protected $attackMax;
  protected $healMin;
  protected $healMax;
  abstract public function sayCry();
  public function setName($str){
    $this->name = $str;
  }
  public function getName(){
    return $this->name;
  }
  public function setHp($num){
    $this->hp = $num;
    if($num > $this->maxhp){
      $this->hp = $this->maxhp;
    }
  }
  public function getMaxHp(){
    return $this->maxhp;
  }
  public function upsetHp($num){
    $this->maxhp = $num;
  }
  public function getHp(){
    return $this->hp;
  }

  public function setAttackMin($num){
    $this->attackMin = $num;
  }

  public function getAttackMin(){
    return $this->attackMin;
  }

  public function setAttackMax($num){
    $this->attackMax = $num;
  }

  public function getAttackMax(){
    return $this->attackMax;
  }

  public function attack($targetObj){
    $attackPoint = mt_rand($this->attackMin, $this->attackMax);
    if(!mt_rand(0,9)){ //10分の1の確率でクリティカル
      $attackPoint = $attackPoint * 1.5;
      $attackPoint = (int)$attackPoint;
      History::set($this->getName().'のクリティカルヒット!!');
    }
    $targetObj->setHp($targetObj->getHp()-$attackPoint);
    History::set($attackPoint.'ポイントのダメージ！');
  }

  public function heal($targetObj){
    $healPoint = mt_rand($this->healMin, $this->healMax);
    $targetObj->setHp($targetObj->getHp()+$healPoint);
    History::set($healPoint.'ポイント回復！');
  }

  // public function superheal($targetObj){
  //   $healPoint = $this->maxhp;
  //   $targetObj->setHp($targetObj->getHp()+$healPoint);
  //   error_log($healPoint);
  //   History::set($healPoint.'回復した！');
  // }
  // public function powerup($targetObj){
  //   $powerup = 20;
  //   $targetObj->setAttackMin($targetObj->getAttackMin()+$powerup);
  //   $targetObj->setAttackMax($targetObj->getAttackMax()+$powerup);
  //   History::set('攻撃力が20上がった！');
    
  // }
  // public function buildup($targetObj){
  //   $buildup = $this->maxhp;
  //   $targetObj->upsetHp($targetObj->getHp()+$buildup);
  //   // $targetObj->setHp($targetObj->getHp()*2);
  //   History::set('HPが'.$buildup.'上がった！');
  // }

}
// 人クラス
class Human extends Creature{
  protected $playertype;
  protected $sex;
  // protected $mp;
  public function __construct($name, $playertype, $sex, $maxhp, $hp, $attackMin, $attackMax, $healMin, $healMax) {
    $this->name = $name;
    $this->playertype = $playertype;
    $this->sex = $sex;
    $this->maxhp = $maxhp;
    $this->hp = $hp;
    $this->attackMin = $attackMin;
    $this->attackMax = $attackMax;
    $this->healMin = $healMin;
    $this->healMax = $healMax;
  }
  public function setSex($num){
    $this->sex = $num;
  }
  public function getSex(){
    return $this->sex;
  }
  public function sayCry(){
    History::set($this->name.'が叫ぶ！');
    switch($this->sex){
      case Sex::MAN :
        History::set('ぐはぁっ！');
        break;
      case Sex::WOMAN :
        History::set('きゃっ！');
        break;
      case Sex::OKAMA :
        History::set('もっと！♡');
        break;
    }
  }
}
//魔法使いクラス
class Wizard extends Human{
  protected $mp;
  public function __construct($name, $playertype, $sex, $maxhp, $hp, $attackMin, $attackMax, $healMin, $healMax){
    parent::__construct($name, $playertype, $sex, $maxhp, $hp, $attackMin, $attackMax, $healMin, $healMax);
    $this->mp = mt_rand(50, 100);
  }
  public function setMp($num){
    $this->mp = $num;
  }
  public function getMp(){
    return $this->mp;
  }
  public function attack($targetObj){
    if(!mt_rand(0,2)){ //3分の1の確率で魔法攻撃
      if($this->mp >= 10){ //MP判定
        if($targetObj->getMonstercategory() === 3){ //飛行モンスターへの魔法攻撃
          error_log('!!!!!!!!!!!'.$_SESSION['monstercategory']);
          $this->mp = $this->mp - 10;
          $attackPoint = mt_rand(mt_rand($this->attackMin, $this->attackMax) * 0.5, mt_rand($this->attackMin, $this->attackMax) * 2) * 1.5;
          $attackPoint = (int)$attackPoint;
          $targetObj->setHp($targetObj->getHp() - $attackPoint);
          History::set($this->name.'の魔法攻撃!!');
          History::set('効果はばつぐんだ!!');
          History::set($attackPoint.'ポイントのダメージ！');
        }else{//魔法攻撃
          $this->mp = $this->mp - 10;
          $attackPoint = mt_rand(mt_rand($this->attackMin, $this->attackMax) * 0.5, mt_rand($this->attackMin, $this->attackMax) * 2);
          $attackPoint = (int)$attackPoint;
          $targetObj->setHp( $targetObj->getHp() - $attackPoint);
          History::set($this->name.'の魔法攻撃!!');
          History::set($attackPoint.'ポイントのダメージ！');
        }
      }else{
        History::set('MPが足りない！');
        parent::attack($targetObj);
      }
    }else{
      parent::attack($targetObj);
    }
  }
}
// モンスタークラス
class Monster extends Creature{
  // プロパティ
  protected $monstercategory;
  protected $img;
  // コンストラクタ
  public function __construct($name, $monstercategory, $maxhp, $hp, $img, $attackMin, $attackMax, $healMin, $healMax) {
    $this->name = $name;
    $this->monstercategory = $monstercategory;
    $this->maxhp = $maxhp;
    $this->hp = $hp;
    $this->img = $img;
    $this->attackMin = $attackMin;
    $this->attackMax = $attackMax;
    $this->healMin = $healMin;
    $this->healMax = $healMax;
  }
  // public function setMonstercategory($num){
  //   $this->monstercategory = $num;
  // }
  public function getMonstercategory(){
    return $this->monstercategory;
  }
  // ゲッター
  public function getImg(){
    return $this->img;
  }
  public function sayCry(){
    History::set($this->name.'が叫ぶ！');
    History::set('はうっ！');
  }
}
// 魔法を使えるモンスタークラス
class MagicMonster extends Monster{
  private $magicAttack;
  function __construct($name, $monstercategory, $maxhp, $hp, $img, $attackMin, $attackMax, $healMin, $healMax, $magicAttack) {
    parent::__construct($name, $monstercategory, $maxhp, $hp, $img, $attackMin, $attackMax, $healMin, $healMax);
    $this->magicAttack = $magicAttack;
  }
  public function getMagicAttack(){
    return $this->magicAttack;
  }
  public function attack($targetObj){
    if(!mt_rand(0,4)){ //5分の1の確率で魔法攻撃
      History::set($this->name.'の魔法攻撃!!');
      $targetObj->setHp($targetObj->getHp() - $this->magicAttack );
      History::set($this->magicAttack.'ポイントのダメージ！');
    }else{
      parent::attack($targetObj);
    }
  }
}
//飛行モンスタークラス
class FlyingMonster extends Monster{
  function __construct($name, $monstercategory, $maxhp, $hp, $img, $attackMin, $attackMax, $healMin, $healMax) {
    parent::__construct($name, $monstercategory, $maxhp, $hp, $img, $attackMin, $attackMax, $healMin, $healMax);
  }
  public function attack($targetObj){
    if(!mt_rand(0,2)){ //3分の1の確率で飛行攻撃
      History::set($this->name.'の飛行攻撃!!');
      $attackPoint = mt_rand($this->attackMin, $this->attackMax) * 1.2;
      $attackPoint = (int)$attackPoint;
      $targetObj->setHp($targetObj->getHp() - $attackPoint);
      History::set($attackPoint.'ポイントのダメージ！');
      parent::setHp(parent::getHp() - 20);
      History::set($this->name.'は20ポイントの反動を受けた！');
      if($this->hp <= 0){
        History::set($_SESSION['monster']->getName().'を倒した！');
        $_SESSION['knockDownCount'] = $_SESSION['knockDownCount']+1;
        createCreature();
      }
    }else{
      parent::attack($targetObj);
    }
  }
}
//ボスモンスタークラス
class BossMonster extends Monster{
  function __construct($name, $monstercategory, $maxhp, $hp, $img, $healMin, $healMax) {
    $attackMin = 50;
    $attackMax = 80;

    error_log($_SESSION['knockDownCount'].'ボスHP');
    $hp = $hp + ($_SESSION['knockDownCount'] * 10);
    parent::__construct($name, $monstercategory, $maxhp, $hp, $img, $attackMin, $attackMax, $healMin, $healMax);
  }
}
//神様クラス
class God{
  public static $name = '神様';
  public static $img = 'img/god.png';
  
  public function getName(){
    return self::$name;
  }
  public function getImg(){
    return self::$img;
  }
  public function superheal($targetObj){
    $targetObj->setHp($targetObj->getHp() + $targetObj->getMaxHp());
    History::set('回復した！');
  }
  public function Powerup($targetObj){
    $powerup = 20;
    $targetObj->setAttackMin($targetObj->getAttackMin()+$powerup);
    $targetObj->setAttackMax($targetObj->getAttackMax()+$powerup);
    History::set('攻撃力が20上がった！');
  }
  public function Buildup($targetObj){
    $targetObj->upsetHp($targetObj->getMaxHp() + $targetObj->getMaxHp());
    // $targetObj->setHp($targetObj->getHp()*2);
    // History::set('HPが'.upsetHp().'上がった！');
  }
}
interface HistoryInterface{
  public static function set($str);
  public static function clear();
}
// 履歴管理クラス（インスタンス化して複数に増殖させる必要性がないクラスなので、staticにする）
class History implements HistoryInterface{
  // public function set($str){
  public static function set($str){
    // セッションhistoryが作られてなければ作る
    if(empty($_SESSION['history'])) $_SESSION['history'] = '';
    // 文字列をセッションhistoryへ格納
    $_SESSION['history'] .= $str.'<br>';
  }
  // public function clear(){
  public static function clear(){
    unset($_SESSION['history']);
  }
}

// インスタンス生成
$humans[] = new Human('勇者', Playertype::BRAVE, Sex::MAN, 500, 500, 40, 120, 10, 100);
$humans[] = new Wizard('魔法使い', Playertype::WIZARD, Sex::WOMAN, 300, 300, 40, 120, 10, 100);
$monsters[] = new Monster( 'フランケン', Monstercategory::BASIC, 100, 100, 'img/monster01.png', 20, 40, 10, 100 );
$monsters[] = new MagicMonster( 'フランケンNEO', Monstercategory::MAGIC, 300, 300, 'img/monster02.png', 20, 60, 10, 100, mt_rand(50, 100) );
$monsters[] = new Monster( 'ドラキュリー', Monstercategory::BASIC, 200, 200, 'img/monster03.png', 30, 50, 10, 100 );
$monsters[] = new MagicMonster( 'ドラキュラ男爵', Monstercategory::MAGIC, 400, 400, 'img/monster04.png', 50, 80, 10, 100, mt_rand(60, 120) );
$monsters[] = new FlyingMonster( 'アルカード', Monstercategory::FLY, 350, 350, 'img/monster04.png', 40, 100, 10, 100 );
$monsters[] = new Monster( 'スカルフェイス', Monstercategory::BASIC, 150, 150, 'img/monster05.png', 30, 60, 10, 100 );
$monsters[] = new FlyingMonster( 'フライングスケルトン', Monstercategory::FLY, 125, 125, 'img/monster05.png', 20, 40, 10, 100 );
$monsters[] = new Monster( '毒ハンド', Monstercategory::BASIC, 100, 100, 'img/monster06.png', 10, 30, 10, 100 );
$monsters[] = new Monster( '泥ハンド', Monstercategory::BASIC, 120, 120, 'img/monster07.png', 20, 30, 10, 100 );
$monsters[] = new Monster( '血のハンド', Monstercategory::BASIC, 180, 180, 'img/monster08.png', 30, 50, 10, 100 );
$god = new God( '神様', 'img/god.png');

function createCreature(){
  global $monsters;
  global $god;


  error_log($_SESSION['knockDownCount'].'モンスター出現時');
  if($_SESSION['knockDownCount'] >= 5 && count($monsters) == 10){
    $monsters[] = new BossMonster('Dracula', Monstercategory::BOSS, 500, 500, 'img/bossmonster.png', 50, 80, 10, 100);
  }
  if($_SESSION['knockDownCount'] >= 5){
    if(!mt_rand(0, 11)){
      unset($_SESSION['monster']);
      History::set($god->getName().'が現れた！');
      $_SESSION['god'] = $god;
    }else{
      $monster = $monsters[mt_rand(0, count($monsters)-1)];
      History::set($monster->getName().'が現れた！');
      $_SESSION['monster'] = $monster;
      $_SESSION['monstercategory'] = $_SESSION['monster']->getMonstercategory();
    }
  }else{
    if(!mt_rand(0, 10)){
      unset($_SESSION['monster']);
      History::set($god->getName().'が現れた！');
      $_SESSION['god'] = $god;
    }else{
      $monster = $monsters[mt_rand(0, count($monsters)-1)];
      History::set($monster->getName().'が現れた！');
      $_SESSION['monster'] = $monster;
      $_SESSION['monstercategory'] = $_SESSION['monster']->getMonstercategory();
    }
  }
}
function createHuman(){
  global $humans;
  if($_SESSION['playertype'] === 1){
    $_SESSION['human'] = $humans[0];
  }else{
    $_SESSION['human'] = $humans[1];
  }
}
function init(){
  History::clear();
  History::set('初期化します！');
  $_SESSION['knockDownCount'] = 0;
  $_SESSION['healcount'] = 0;
  createHuman();
  createCreature();
}


//1.post送信されていた場合
if(!empty($_POST)){
  // $startFlg = (!empty($_POST['start'])) ? true : false;
  $BstartFlg = (!empty($_POST['brave'])) ? true : false;
  $WstartFlg = (!empty($_POST['wizard'])) ? true : false;
  $attackFlg = (!empty($_POST['attack'])) ? true : false;
  $healFlg = (!empty($_POST['heal'])) ? true : false;
  $escapeFlg = (!empty($_POST['escape'])) ? true : false;
  $superhealFlg = (!empty($_POST['superheal'])) ? true : false;
  $powerupFlg = (!empty($_POST['powerup'])) ? true : false;
  $buildupFlg = (!empty($_POST['buildup'])) ? true : false;
  $clearFlg = 0;
  $gameoverFlg = 0;
  
  //プレイヤー選択処理
  if($BstartFlg){
    $_SESSION['playertype'] = 1;
    init();
    History::set('勇者でゲームスタート！');
  }else if($WstartFlg){
    $_SESSION['playertype'] = 2;
    init();
    History::set('魔法使いでゲームスタート！');
  }

  //神様が出ていないときのアクション
  if($attackFlg){
      
    // モンスターに攻撃を与える
    History::set($_SESSION['human']->getName().'の攻撃！');
    $_SESSION['human']->attack($_SESSION['monster']);
    $_SESSION['monster']->sayCry();

    
    // モンスターが攻撃をする
    if($_SESSION['monster']->getHp() >= 0){
      History::set($_SESSION['monster']->getName().'の攻撃！');
      $_SESSION['monster']->attack($_SESSION['human']);
      $_SESSION['human']->sayCry();
    }else if($_SESSION['monster']->getHp() <= 0){
      if($_SESSION['monstercategory'] === 4){
        $clearFlg = true;
        History::clear();
        error_log($clearFlg);
      }else{
        History::set($_SESSION['monster']->getName().'を倒した！');
        error_log($_SESSION['knockDownCount'].'増える前');
        $_SESSION['knockDownCount'] = $_SESSION['knockDownCount']+1;
        error_log($_SESSION['knockDownCount'].'増えた後');
        createCreature();
      }
    }
    
    // 自分のhpが0以下になったらゲームオーバー
    if($_SESSION['human']->getHp() <= 0){
      debug('攻撃した後勇者のHP0');
      $gameoverFlg = true; 
    }
  
  }else if($healFlg){// 回復するを押した場合

    if($_SESSION['healcount'] >= 3){

      History::set('もう回復できません！');

    }else{
      // 回復する
      History::set($_SESSION['human']->getName().'の回復！');
      $_SESSION['human']->heal($_SESSION['human']);
      $_SESSION['healcount'] += 1;
    }

    // モンスターが攻撃をする
    History::set($_SESSION['monster']->getName().'の攻撃！');
    $_SESSION['monster']->attack($_SESSION['human']);
    $_SESSION['human']->sayCry();

    // 自分のhpが0以下になったらゲームオーバー
    if($_SESSION['human']->getHp() <= 0){
      debug('回復した後勇者のHP0');
      $gameoverFlg = true;
    }

  }else if($escapeFlg){ //逃げるを押した場合
    History::set('逃げた！');
    createCreature();
  }

  //神様が出ているときのアクション
  if($superhealFlg){// 神：回復してもらうを押した場合

    History::set($_SESSION['god']->getName().'が回復してくれた！');
    $_SESSION['god']->superheal($_SESSION['human']);
    createCreature();

  }else if($powerupFlg){// 神：強くしてもらうを押した場合

    History::set($_SESSION['god']->getName().'が強くしてくれた！');
    $_SESSION['god']->powerup($_SESSION['human']);
    createCreature();

  }else if($buildupFlg){// 神：丈夫にしてもらうを押した場合

    History::set($_SESSION['god']->getName().'に丈夫にしてもらった！');
    $_SESSION['god']->buildup($_SESSION['human']);
    createCreature();

  }
}
  
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>ホームページのタイトル</title>
    <style>
    	body{
	    	margin: 0 auto;
	    	padding: 150px;
	    	width: 25%;
	    	background: #fbfbfa;
        color: white;
    	}
    	h1{ color: white; font-size: 20px; text-align: center;}
      h2{ color: white; font-size: 16px; text-align: center;}
      h4{ color: white; font-size: 8px; text-align: center;}
    	form{
	    	overflow: hidden;
    	}
    	input[type="text"]{
    		color: #545454;
	    	height: 60px;
	    	width: 100%;
	    	padding: 5px 10px;
	    	font-size: 16px;
	    	display: block;
	    	margin-bottom: 10px;
	    	box-sizing: border-box;
    	}
      input[type="password"]{
    		color: #545454;
	    	height: 60px;
	    	width: 100%;
	    	padding: 5px 10px;
	    	font-size: 16px;
	    	display: block;
	    	margin-bottom: 10px;
	    	box-sizing: border-box;
    	}
    	input[type="submit"]{
        border: none;
        width: 70%;
	    	padding: 15px 30px;
	    	margin-bottom: 15px;
	    	background: black;
	    	color: white;
	    	float: right;
    	}
    	input[type="submit"]:hover{
	    	background: #3d3938;
	    	cursor: pointer;
    	}
    	a{
	    	color: #545454;
	    	display: block;
    	}
    	a:hover{
	    	text-decoration: none;
      }
      .modal{
        display: none;
        width: 90%;
        height: 250px;
        background: #f6f5f4;
        padding: 15px;
        box-sizing: border-box;
        position: absolute;
        top: 10%;
        z-index: 1;
      }
      .cover{
        background: rgba(0, 0, 0, 0.5);
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        display: none;
      }
      .btn{
        font-size: 14px;
        background: black;
        color: white;
        border: none;
        padding: 15px;
        margin: 10px auto;
        display: block;
      }
    </style>
  </head>
  <body onload="load()">
   <h1 style="text-align:center; color:#333;">ゲーム「ドラ◯エ!!」</h1>
    <div style="background:black; padding:15px; position:relative;">
      <?php if(empty($_SESSION)){ ?>
        <h2 style="margin-top:60px;">GAME START ?</h2>
        <button class="btn js-show-modal">START</button>
          <div class="modal js-show-modal-target">
            <h2 style="color:#000;">選択してください</h2>
              <form method="post">
                <input type="submit" name="brave" class="js-hide-modal js-set-playCount" style="margin-top:15px; margin-right:15%;" value="▶勇者でゲームスタート">
                <input type="submit" name="wizard" class="js-hide-modal js-set-playCount" style="margin-top:10px; margin-right:15%;" value="▶魔法使いでゲームスタート">
              </form>
          </div>
          <h4 style="text-align: center">プレイ回数：<span class="js-get-playCount"></span>回</h4>
          <h4 style="text-align: center">クリア回数：<span class="js-get-clearCount"></span>回</h4>
          <h4 style="text-align: center">ゲームオーバー回数：<span class="js-get-gameoverCount"></span>回</h4>
      <?php }else if($clearFlg){ ?>
        <h2 style="margin-top:60px;" class="js-set-clearCount" id="gameclear">GAME CLEAR !</h2>
        <button class="btn js-show-modal">START</button>
          <div class="modal js-show-modal-target">
            <h2 style="color:#000;">選択してください</h2>
              <form method="post">
                <input type="submit" name="brave" class="js-hide-modal js-set-playCount" style="margin-top:15px; margin-right:15%;" value="▶勇者でゲームスタート">
                <input type="submit" name="wizard" class="js-hide-modal js-set-playCount" style="margin-top:10px; margin-right:15%;" value="▶魔法使いでゲームスタート">
              </form>
          </div>
          <h4 style="text-align: center">プレイ回数：<span class="js-get-playCount"></span>回</h4>
          <h4 style="text-align: center">クリア回数：<span class="js-get-clearCount" id="clear1"></span>回</h4>
          <h4 style="text-align: center">ゲームオーバー回数：<span class="js-get-gameoverCount"></span>回</h4>
        <?php }else if($gameoverFlg){ ?>
        <h2 style="margin-top:60px;" class="js-set-gameoverCount" id="gameover">GAME OVER !</h2>
        <button class="btn js-show-modal">START</button>
          <div class="modal js-show-modal-target">
            <h2 style="color:#000;">選択してください</h2>
              <form method="post">
                <input type="submit" name="brave" class="js-hide-modal js-set-playCount" style="margin-top:15px; margin-right:15%;" value="▶勇者でゲームスタート">
                <input type="submit" name="wizard" class="js-hide-modal js-set-playCount" style="margin-top:10px; margin-right:15%;" value="▶魔法使いでゲームスタート">
              </form>
          </div>
          <h4 style="text-align: center">プレイ回数：<span class="js-get-playCount"></span>回</h4>
          <h4 style="text-align: center">クリア回数：<span class="js-get-clearCount"></span>回</h4>
          <h4 style="text-align: center">ゲームオーバー回数：<span class="js-get-gameoverCount" id="gameover1"></span>回</h4>
      <?php }else if(!empty($_SESSION['monster'])){ ?>
        <h2><?php echo $_SESSION['monster']->getName().'が現れた!!'; ?></h2>
        <div style="height: 150px;">
          <img src="<?php echo $_SESSION['monster']->getImg(); ?>" style="width:120px; height:auto; margin:40px auto 0 auto; display:block;">
        </div>
        <?php if($_SESSION['monstercategory'] === 4){ ?>
          <p style="font-size:14px; text-align:center;">ボスモンスターのHP：<?php echo $_SESSION['monster']->getHp(); ?></p>
        <?php }else{ ?>
        <p style="font-size:14px; text-align:center;">モンスターのHP：<?php echo $_SESSION['monster']->getHp(); ?></p>
        <?php } ?>
        <p>倒したモンスター数：<?php echo $_SESSION['knockDownCount']; ?></p>
        <?php if($_SESSION['playertype'] === 1){ ?>
          <p>勇者の残りHP：<?php echo $_SESSION['human']->getHp(); ?></p>
        <?php }else{ ?>
          <p>魔法使いの残りHP：<?php echo $_SESSION['human']->getHp(); ?></p>
        <?php } ?>
        <?php if($_SESSION['playertype'] === 2){ ?>
          <p>魔法使いの残りMP：<?php echo $_SESSION['human']->getMp(); ?></p>
        <?php } ?>
        <form method="post">
          <input type="submit" name="heal" value="▶回復する">
          <input type="submit" name="attack" value="▶攻撃する">
          <input type="submit" name="escape" value="▶逃げる">
          <?php if($_SESSION['playertype'] === 1){ ?>
          <input type="submit" name="brave" value="▶ゲームリスタート">
        <?php }else{ ?>
          <input type="submit" name="wizard" value="▶ゲームリスタート">
        <?php } ?>
        </form>
        <?php }else{ ?>
        <h2><?php echo $_SESSION['god']->getName().'が現れた!!'; ?></h2>
        <div style="height: 150px;">
          <img src="<?php echo $_SESSION['god']->getImg(); ?>" style="width:120px; height:auto; margin:40px auto 0 auto; display:block;">
        </div>
        <p style="font-size:14px; text-align:center;">選べ！！</p>
        <p>倒したモンスター数：<?php echo $_SESSION['knockDownCount']; ?></p>
        <?php if($_SESSION['playertype'] === 1){ ?>
          <p>勇者の残りHP：<?php echo $_SESSION['human']->getHp(); ?></p>
        <?php }else{ ?>
          <p>魔法使いの残りHP：<?php echo $_SESSION['human']->getHp(); ?></p>
        <?php } ?>
        <?php if($_SESSION['playertype'] === 2){ ?>
          <p>魔法使いの残りMP：<?php echo $_SESSION['human']->getMp(); ?></p>
        <?php } ?>
        <form method="post">
          <input type="submit" name="superheal" value="▶回復してもらう">
          <input type="submit" name="powerup" value="▶強くしてもらう">
          <input type="submit" name="buildup" value="▶丈夫にしてもらう">
        <?php if($_SESSION['playertype'] === 1){ ?>
          <input type="submit" name="brave" value="▶ゲームリスタート">
        <?php }else{ ?>
          <input type="submit" name="wizard" value="▶ゲームリスタート">
        <?php } ?>
        </form>
      <?php } ?>
      <div style="position:absolute; right:-350px; top:0; color:black; width: 300px;">
        <p><?php echo (!empty($_SESSION['history'])) ? $_SESSION['history'] : ''; ?></p>
      </div>
    </div>
    <div class="cover js-show-modal-cover"></div>
          <script
                  src="https://code.jquery.com/jquery-3.4.1.min.js"
                  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
                  crossorigin="anonymous"></script>
          <script src="jquery-cookie-master/src/jquery.cookie.js"></script> 
          <script src="app.js"></script>
  </body>
</html>
