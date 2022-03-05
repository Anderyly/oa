<?php

namespace app\service\controller;

use ay\lib\Db;

class Job {
	
	public function index() {
		// $hour = date('H', time());
		// if ($hour > 1) exit('time out');
		$res = Db::name('product')->field('scan,plus,pid')->where([['scan', '!=', 0], ['plus', '!=', 0]])->select();
		foreach ($res as $k => $v) {
			if ($v['scan'] != 0 or $v['plus'] != 0) {
				Db::name('product')->where('pid', $v['pid'])->update(['scan' => 0, 'plus' => 0]);
			}
		}
		echo 'ok';
	}
	
}