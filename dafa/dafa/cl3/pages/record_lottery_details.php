<?php 
include_once($C_Patch."/resource/lottery/getContentName.php");

$start_time = $date_POST." 00:00:00";
$end_time = $date_POST." 23:59:59";
$sql	=	"SELECT o.Gtype,o.lottery_number AS qishu,o.rtype_str,o.bet_time,o.order_num,o_sub.quick_type,
                        o_sub.number,o_sub.bet_money AS bet_money_one,o_sub.fs,
                        o_sub.bet_rate AS bet_rate_one,o_sub.is_win,o_sub.status,
                        o_sub.id AS id,o_sub.win AS win_sub,o_sub.balance,o_sub.order_sub_num
              FROM order_lottery o,order_lottery_sub o_sub
              WHERE o.bet_time>='$start_time' and o.bet_time<='$end_time' AND o.order_num=o_sub.order_num AND o.user_id='".$_SESSION["userid"]."' AND o.Gtype='".$_REQUEST["gtype"]."'
              order by o_sub.status asc,o_sub.id desc";
$query	=	$mysqli->query($sql);
while($row = $query->fetch_array()){
    $user_lottery_list[] = $row;
}
$subPage = '';
$t_allmoney=0;
$t_sy=0;
if($user_lottery_list && count($user_lottery_list)>0){
    foreach ($user_lottery_list as $key =>$rows) {
        $t_allmoney+=$rows['bet_money_one'];
        $money_result = 0;
        if($rows['is_win']=="1"){
            $t_sy= $t_sy + $rows['win_sub'] + $rows['fs'];
            $money_result = $rows['win_sub'] + $rows['fs'];
        }elseif($rows['is_win']=="2"){
            $t_sy+=$rows['bet_money_one'];
            $money_result = $rows['bet_money_one'];
        }elseif($rows['is_win']=="0" && $rows['fs']>0){
            $t_sy+=$rows['fs'];
            $money_result = $rows['fs'];
        }

        $contentName = getName($rows['number'],$rows['Gtype'],$rows['rtype_str'],$rows['quick_type']);
        $bet_time = substr($rows["bet_time"],11);

        $bet_rate = $rows['bet_rate_one'];
        if(strpos($bet_rate,",") !== false){
            $bet_rate_array = explode(",", $bet_rate);
            $bet_rate = $bet_rate_array[0];
        }

        if($rows['status']==0){
            $status_result = "未结算";
        }elseif($rows['status']==1){
            $status_result = "已结算";
        }elseif($rows['status']==2){
            $status_result = "已结算";
        }elseif($rows['status']==3){
            $status_result = "作废";
        }else{
            $status_result = "未结算";
        }

        $subPage = $subPage.'
    <tr >
    <td style="text-align:center;width: 130px;padding-left: 0px;padding-right: 0px;">'.$rows["order_sub_num"].'</td>
    <td style="text-align:center;width: 80px;padding-left: 0px;padding-right: 0px;">'.$rows["qishu"].'</td>
    <td style="text-align:center;padding-left: 2px;padding-right: 2px;">'.$rows["rtype_str"].'</td>
    <td style="text-align:center;padding-left: 2px;padding-right: 2px;">'.$contentName.'</td>
    <td style="text-align:center;padding-left: 2px;padding-right: 2px;">'.$rows["bet_money_one"].'</td>
    <td style="text-align:center;padding-left: 2px;padding-right: 2px;">'.$rows["fs"].'</td>
    <td style="text-align:center;padding-left: 2px;padding-right: 2px;">'.$bet_rate.'</td>
    <td style="text-align:center;padding-left: 2px;padding-right: 2px;">'.$money_result.'</td>
    <td style="text-align:center;width: 58px;padding-left: 0px;padding-right: 0px;">'.$bet_time.'</td>
    <td style="text-align:center;width: 45px;padding-left: 0px;padding-right: 0px;">'.$status_result.'</td>
    </tr>';
    }
}else{
    $subPage = '<td colspan="10" style="text-align:center;">暂时没有下注信息。</td>';
}
?>

<div id="MACenterContent">
    <div id="MNav">
        <span class="mbtn" >投注记录</span>
        <div class="navSeparate"></div>
    </div>
    <div id="MNavLv2">
		<span class="ABameType " onclick="chgType('liveHistory');">AG记录</span>｜
		<span class="ABameType" onclick="chgType('liveHistory11');">BBIN记录</span>｜
		<span class="ABameType" onclick="chgType('liveHistory22');">MG记录</span>｜
		<span class="ABameType " onclick="chgType('abliveHistory');">ALLBET记录</span>｜
		<span class="ABameType" onclick="chgType('ptliveHistory');">PT记录</span>｜
		<span class="ABameType" onclick="chgType('naliveHistory');">NA记录</span>｜
		<span class="ABameType" onclick="chgType('skRecord');">彩票记录</span>｜
		<span class="ABameType  " onclick="chgType('bhdzliveHistory');">棋牌记录</span>｜
		<span class="ABameType" onclick="chgType('ballRecord');">体育记录</span>｜
		<span class="ABameType MCurrentType" onclick="chgType('fsrecord');">反水记录</span>
    </div>
    <div id="MMainData">
        <div class="MControlNav">
            <select disabled="disabled">
                <option label="<?=$date_POST?>" selected="selected"><?=$date_POST?></option>
            </select>
            <?php 
            $gTypeName = getZhPageTitle($_REQUEST["gtype"]);
            ?>
            <select disabled="disabled">
                <option label="<?=$gTypeName?>" selected="selected"><?=$gTypeName?></option>
            </select>

            <input type="button" class="MBtnStyle" value="上一页" onclick="f_com.MChgPager({type: 'POST', method: 'SKLotteryHistory'}, {date: '<?=$date_POST?>'});" onmouseover="mover(this);" onmouseout="mout(this);" />
        </div>

        <table class="MMain" border="1">
            <thead>
            <tr>
                <th>订单号</th>
                <th>彩票期号</th>
                <th>投注玩法</th>
                <th>投注内容</th>
                <th>投注金额</th>
                <th>反水</th>
                <th>赔率</th>
                <th>输赢结果</th>
                <th>投注时间</th>
                <th>状态</th>
            </tr>
            </thead>
            <tbody id="general-msg">
            <?=$subPage?>
            </tbody>
            <tfoot id="msgfoot" style="display:none;">
            <tr><td colspan='10' style='text-align:center;'></td></tr>
            </tfoot>
        </table>
    </div>
</div>

<script language="javascript">
    var oMsg = {
        "totalPage": {},    //總頁數
        "pageMsg": 50,      //每頁顯示訊息數
        "msglist": $('#general-msg'),
        'currentPage': 1,    //當前頁碼
        "page": function(p) {
            this.msglist.find("tr").css({"background-color": ""});
            $(".msgcontent").remove();
            oMsg.currentPage = p;
            this.totalPage = Math.ceil(this.msglist.find("tr").length / this.pageMsg);

            if(this.totalPage > 1) {
                $("#msgfoot").show();
            }
            if(this.totalPage == 1) {
                $("#msgfoot").hide();
            }
            $("#msgfoot tr td").html("");
            oMsg.msglist.find("tr").hide();

            //判斷最後一頁是否有筆數
            if(oMsg.currentPage > this.totalPage) {
                oMsg.currentPage = this.totalPage ;
            }
            for(var i = ((oMsg.currentPage-1) * oMsg.pageMsg ) ; i < oMsg.pageMsg + ((oMsg.currentPage - 1) * oMsg.pageMsg); i++) {
                oMsg.msglist.find("tr:eq(" + i + ")").show();
            }
            for(var t = 1 ; t <= this.totalPage ; t++) {
                if(oMsg.currentPage == t) {
                    $("#msgfoot tr td").append("<span id='currentpage'>" + t + "</span>");
                } else {
                    $("#msgfoot tr td").append("<a class='pagelink' href='#' onclick='oMsg.page(" + t + ")'>" + t + "</a>");
                }
            }
        }
    }

    oMsg.page(oMsg.currentPage);

    $(".MMain tbody tr").hover(function(){
        $("td", this).addClass("mouseenter");
        $("td a", this).addClass("mouseenter");
    }, function() {
        $("td", this).removeClass("mouseenter");
        $("td a", this).removeClass("mouseenter");
    });

    var GAMESELECT = "SKLotteryHistoryDetails"
    //選擇遊戲
    $("#MSelectType").change(function() {
        switch(GAMESELECT) {
            case 'SKRecord':
            case 'SKLotteryRecord':
                f_com.MChgPager({method: 'SKHistory'});
                break;
            case 'SKHistory':
            case 'SKLotteryHistory':
                f_com.MChgPager({method: 'SKRecord'});
                break;
        }
    });

    function chgType(type) {
        switch(type) {
            case 'ballRecord':
                f_com.MChgPager({method: 'ballRecord'});
                break;
            case 'lotteryRecord':
                f_com.MChgPager({method: 'lotteryRecord'});
                break;
            case 'liveHistory':
                f_com.MChgPager({method: 'liveHistory'});
                break;
            case 'gameHistory':
                f_com.MChgPager({method: 'gameHistory'});
                break;
            case 'skRecord':
                f_com.MChgPager({method: 'skRecord'});
                break;
            case 'a3dhHistory':
                f_com.MChgPager({method: 'a3dhHistory'});
                break;
            case 'TPBFightHistory':
                f_com.MChgPager({method: 'TPBFightHistory'});
                break;
            case 'TPBSPORTHistory':
                f_com.MChgPager({method: 'TPBSPORTHistory'});
                break;
            case 'cqRecord':
                f_com.MChgPager({method: 'cqRecord'});
                break;
        }
    }
</script>