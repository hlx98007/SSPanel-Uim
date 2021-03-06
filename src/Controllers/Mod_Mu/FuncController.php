<?php

namespace App\Controllers\Mod_Mu;

use App\Controllers\BaseController;
use App\Models\{
    Auto,
    Node,
    BlockIp,
    UnblockIp,
    Speedtest,
    DetectRule
};

class FuncController extends BaseController
{
    public function ping($request, $response, $args)
    {
        $res = [
            'ret' => 1,
            'data' => 'pong'
        ];
        return $this->echoJson($response, $res);
    }

    public function get_detect_logs($request, $response, $args)
    {
        $rules = DetectRule::all();

        $res = [
            'ret' => 1,
            'data' => $rules
        ];
        return $this->echoJson($response, $res);
    }

    public function get_dis_node_info($nodeid)
    {
        $node = Node::where('id', $nodeid)->first();
        if ($node == null) {
            return null;
        }

        return $node;
    }

    public function get_blockip($request, $response, $args)
    {
        $block_ips = BlockIp::Where('datetime', '>', time() - 60)->get();

        $res = [
            'ret' => 1,
            'data' => $block_ips
        ];
        return $this->echoJson($response, $res);
    }

    public function get_unblockip($request, $response, $args)
    {
        $unblock_ips = UnblockIp::Where('datetime', '>', time() - 60)->get();

        $res = [
            'ret' => 1,
            'data' => $unblock_ips
        ];
        return $this->echoJson($response, $res);
    }

    public function addBlockIp($request, $response, $args)
    {
        $params = $request->getQueryParams();

        $data = $request->getParam('data');
        $node_id = $params['node_id'];
        if ($node_id == '0') {
            $node = Node::where('node_ip', $_SERVER['REMOTE_ADDR'])->first();
            $node_id = $node->id;
        }
        $node = Node::find($node_id);
        if ($node == null) {
            $res = [
                'ret' => 0
            ];
            return $this->echoJson($response, $res);
        }

        if (count($data) > 0) {
            foreach ($data as $log) {
                $ip = $log['ip'];

                $exist_ip = BlockIp::where('ip', $ip)->first();
                if ($exist_ip != null) {
                    continue;
                }

                // log
                $ip_block = new BlockIp();
                $ip_block->ip = $ip;
                $ip_block->nodeid = $node_id;
                $ip_block->datetime = time();
                $ip_block->save();
            }
        }

        $res = [
            'ret' => 1,
            'data' => 'ok',
        ];
        return $this->echoJson($response, $res);
    }

    public function addSpeedtest($request, $response, $args)
    {
        $params = $request->getQueryParams();

        $data = $request->getParam('data');
        $node_id = $params['node_id'];
        if ($node_id == '0') {
            $node = Node::where('node_ip', $_SERVER['REMOTE_ADDR'])->first();
            $node_id = $node->id;
        }
        $node = Node::find($node_id);
        if ($node == null) {
            $res = [
                'ret' => 0
            ];
            return $this->echoJson($response, $res);
        }

        if (count($data) > 0) {
            foreach ($data as $log) {
                // log
                $speedtest_log = new Speedtest();
                $speedtest_log->telecomping = $log['telecomping'];
                $speedtest_log->telecomeupload = $log['telecomeupload'];
                $speedtest_log->telecomedownload = $log['telecomedownload'];

                $speedtest_log->unicomping = $log['unicomping'];
                $speedtest_log->unicomupload = $log['unicomupload'];
                $speedtest_log->unicomdownload = $log['unicomdownload'];

                $speedtest_log->cmccping = $log['cmccping'];
                $speedtest_log->cmccupload = $log['cmccupload'];
                $speedtest_log->cmccdownload = $log['cmccdownload'];
                $speedtest_log->nodeid = $node_id;
                $speedtest_log->datetime = time();
                $speedtest_log->save();
            }
        }

        $res = [
            'ret' => 1,
            'data' => 'ok',
        ];
        return $this->echoJson($response, $res);
    }
}
