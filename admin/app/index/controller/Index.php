<?php

namespace app\index\controller;

use ay\lib\Db;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\JcTable;
use PhpOffice\PhpWord\SimpleType\VerticalJc;

class Index
{

    public function index()
    {

        $data = [];
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $PHPWordHelper= new \PhpOffice\PhpWord\Style\Font();

        $section = $phpWord->addSection();

        // 段落样式
        $phpWord->addParagraphStyle(
            'Normal',array(
                'spaceBefore' => 0,
                'spaceAfter' => 0,
                'lineHeight' => 1.0,  // 行间距
                'spacing' => 200,
            )
        );

        $spc = 'spc';
        $phpWord->addFontStyle(
            $spc,
            array('name' => 'Times New Roman', 'size' => 10.5)
        );

        $header = $section->createHeader();
        $table = $header->addTable();
        $table->addRow();
        $table->addCell(16800)->addText('房地产估价报告书',
            array('name' => '隶书', 'size' => 10.5, 'color' => '1B2232', 'bold' => true, 'align' => 'right'),
            array(
                'spaceBefore' => 0,
                'spaceAfter' => 0,
                'lineHeight' => 1.0,  // 行间距
                'spacing' => 240,
                'align' => 'center'
            ));


        $footer = $section->createFooter();
        $footer->addPreserveText('江苏苏北土地房地产资产评估测绘咨询有限公司      　　　　    电话：0516-85793968',
            array('name' => '隶书', 'size' => 10.5, 'color' => '1B2232', 'bold' => true, 'align' => 'right'),
            array(
                'spaceBefore' => 0,
                'spaceAfter' => 0,
                'lineHeight' => 1.0,  // 行间距
                'spacing' => 240,
                'align' => 'center'
            ));


        // 第一页
        $this->set1($section, $phpWord, $spc, $data);

        // 第二页
        $this->set2($section, $phpWord, $spc, $data);

        // 第三页
        $this->set3($section, $phpWord, $spc, $data);
        // 第四页
        $this->set4($section, $phpWord, $spc, $data);

        // 第五页
        $this->set5($section, $phpWord, $spc, $data);

        // 第六、七页
        $this->set6and7($section, $phpWord, $spc, $data);

        // 第八页
        $this->set8($section, $phpWord, $spc, $data);

        // 第九页
        $section->addPageBreak();
        $section->addText(
            '房地产估价技术报告',
            'three',
            [
                'spaceBefore' => 0,
                'spaceAfter' => 0,
                'lineHeight' => 1.0,  // 行间距
                'spacing' => 240,
                'align' => 'center'
            ]
        );
        $section->addText("一、估价对象区位状况描述与分析", array('name' => '宋体', 'size' => 15, 'bold' => true, 'color' => '1B2232', 'align' => 'center'), 'w100');
        $section->addText("    1.位置状况", 'eightF', 'w100');
        $section->addText("    1.1 坐落 ", 'eightF', 'w100');
        $section->addText("    估价对象为沛县龙润华府小区4-1-101室，坐落于沛县沛县乡镇评税片区，运河路北侧住宅区内的标准房。 ", 'eightF', 'w100');
        $section->addText("    1.2楼层", 'eightF', 'w100');
        $section->addText("    估价对象位于所在建筑物第1层。", 'eightF', 'w100');
        $section->addText("    1.3 临路状况 ", 'eightF', 'w100');
        $section->addText("    估价对象东至S253，南至运河路，西至张庄镇人民政府，北至空地。", 'eightF', 'w100');
        $section->addText("    2. 交通状况", 'eightF', 'w100');
        $section->addText("    2.1 道路状况 ", 'eightF', 'w100');
        $section->addText("    估价对象附近有混合型主干道运河路、S253，交通流量较优。", 'eightF', 'w100');
        $section->addText("    2.2 公共交通的通达程度 ", 'eightF', 'w100');
        $section->addText("    估价对象附近有29路、302路等多条公交线路，至公交站点的距离约为200米，公交班次平均隔10分钟有一辆汽车通过。 ", 'eightF', 'w100');
        $section->addText("    2.3 交通管制情况：通往估价对象附近道路的限制行车速度 60 公里以下。 ", 'eightF', 'w100');
        $section->addText("    2.4 停车方便程度：估价对象所在小区内附近有停车场。", 'eightF', 'w100');
        $section->addText("    3.周围环境状况", 'eightF', 'w100');
        $section->addText("    3.1 自然环境：估价对象所处区域绿化环境一般，周边有大型商业广场。 ", 'eightF', 'w100');
        $section->addText("    3.2 人文环境：估价对象所在区域居民收入水平、文化程度一般，治安状况良好，附近有江南新天地、御景花园、张庄新村等住宅小区。", 'eightF', 'w100');
        $section->addText("    3.3 景观：估价对象所在住宅小区附近景观程度一般。", 'eightF', 'w100');
        $section->addText("    4.外部配套设施状况", 'eightF', 'w100');
        $section->addText("    4.1 基础设施", 'eightF', 'w100');
        $section->addText("    估价对象所在住宅小区，具备道路、给水、排水（雨水、污水）、电力、通信等设施，完备程度较高。", 'eightF', 'w100');
        $section->addText("    4.2 公共配套设施", 'eightF', 'w100');
        $section->addText("    估价对象所在区域公共服务设施齐全，数量较多，有沛县张庄镇农贸市场、嘉年华超市、汉韵广场、苏果超市。", 'eightF', 'w100');
        $section->addText("    4.3教育配套设施", 'eightF', 'w100');
        $section->addText("    估价对象所在区域学区为：沛县张庄镇中心小学、张庄中学", 'eightF', 'w100');
        $section->addText("    5.区位状况优劣性分析", 'eightF', 'w100');
        $section->addText("    估价对象所在区域主要用地类型为住宅、商业，区域内商业聚集度较优。", 'eightF', 'w100');
        $section->addText("二、估价对象实物状况描述与分析", array('name' => '宋体', 'size' => 15, 'bold' => true, 'color' => '1B2232', 'align' => 'center'), 'w100');
        $section->addText("    1.土地基本状况", 'eightF', 'w100');
        $section->addText("    1.1标的土地使用权类型：出让，土地用途：城镇住宅用地，分摊的土地使用权面积：12.0㎡。 ", 'eightF', 'w100');
        $section->addText("    1.2四至：估价对象东至S253，南至运河路，西至张庄镇人民政府，北至空地。 ", 'eightF', 'w100');
        $section->addText("    1.3形状： 较规则多边形。 ", 'eightF', 'w100');
        $section->addText("    1.4地形、地势：估价对象所在宗地地形平坦，地势较高，不易积水。 ", 'eightF', 'w100');
        $section->addText("    1.5地质条件：地质坚实、地基承载力较大且稳定性较强，地下水位和水质未见异常，也未见不良地质现象。 ", 'eightF', 'w100');
        $section->addText("    1.6土壤：未见土壤受过污染，酸碱性适合植物生长。 ", 'eightF', 'w100');
        $section->addText("    1.7基础设施完备及土地平整程度：本次评估宗地实际开发程度为宗地红线内外达通路、通电、通上水、通下水、通讯、通气的“六通”，宗地红线内场地平整的“一平”，本次评估设定开发程度为宗地红线外达到通路、通电、通上水、通下水、通讯、通气的“六通”和宗地红线内场地平整“一平”。", 'eightF', 'w100');
        $section->addText("    2.建筑物实物状况 ", 'eightF', 'w100');
        $section->addText("    2.1名称、用途： ", 'eightF', 'w100');
        $section->addText("    估价对象为坐落于沛县运河路的国有出让住宅用地上的住宅用途房地产。 ", 'eightF', 'w100');
        $section->addText("    2.2建筑类型、建筑面积、建筑结构： ", 'eightF', 'w100');
        $section->addText("    估价对象为普通多层建筑，建筑面积为99.36㎡，钢混结构。 ", 'eightF', 'w100');
        $section->addText("    2.3层数、层高：估价对象位于总楼层为6层的第1层，层高约 2.8 米。 ", 'eightF', 'w100');
        $section->addText("    2.4外观形象：估价对象造型普通，外墙抹面为涂料。 ", 'eightF', 'w100');
        $section->addText("    2.5建筑功能 ", 'eightF', 'w100');
        $section->addText("    2.5.1日照、采光：白天室内明亮，室内有一定的空间能获得一定时间的太阳光照射。 ", 'eightF', 'w100');
        $section->addText("    2.5.2通风：能够使室内与室外空气之间流通，保持室内空气新鲜。 ", 'eightF', 'w100');
        $section->addText("    2.5.3保温、隔热：冬季能保温，夏季能隔热、放热； ", 'eightF', 'w100');
        $section->addText("    2.5.4隔声：能阻隔声音在室内与室外之间、上下楼层之间、左右隔壁之间、室内各房间之间的传递，有效防止噪声和保护私密性； ", 'eightF', 'w100');
        $section->addText("    2.5.5防水：屋顶或楼板不漏水，外墙不渗雨； ", 'eightF', 'w100');
        $section->addText("    诸上方面性能总体良好。 ", 'eightF', 'w100');
        $section->addText("    2.5.6 空间布局", 'eightF', 'w100');
        $section->addText("    估价对象的平面设计中的功能分区合理，使用方便，其户型为三室一厅一厨一卫，二室朝南，客厅在中间，厨房卫生间朝北，全明。 ", 'eightF', 'w100');
        $section->addText("    2.5.7 设施设备、智能化程度、物业服务水平 ", 'eightF', 'w100');
        $section->addText("    估价对象设有楼梯，具备供水、排水、供电、通信、网络等设施，设施较齐备，智能化程度一般，有物业。 ", 'eightF', 'w100');
        $section->addText("    2.5.8 建成年代、维护保养情况和完损状况 ", 'eightF', 'w100');
        $section->addText("    估价对象建成于2016年，基础较为稳固，未见沉降；墙面、地面、门窗等保养完好，综合保养成新度为八五成新。 ", 'eightF', 'w100');
        $section->addText("三、市场背景描述与分析", array('name' => '宋体', 'size' => 15, 'bold' => true, 'color' => '1B2232', 'align' => 'center'), 'w100');
        $section->addText("    1.宏观经济政策 ", 'eightF', 'w100');
        $section->addText("    随着宏观经济政策的明朗，有关侧记经济政策的出台，宏观经济将筑底回升，中国人民银行于 2015 年 10 月 24 日起基准利率水平调整为： ", 'eightF', 'w100');

        $textrun = $section->addTextRun();
        $textrun->addImage(PUB . 'image/table1.png', ["width" => 450, 'height' => 100]);

        $section->addText("    2．住宅房地产交易税收政策", 'eightF', 'w100');
        $section->addText("    2.1 个人购入房地产", 'eightF', 'w100');
        $section->addText("    自 2016 年 2 月 22 日起，对个人购买家庭唯一住房（家庭成员范围包括购房人、配偶以及未成年子女，下同）,面积为 90平方米及以下的，减按 1%的税率征收契税；面积为 90平方米以上的，减按 1.5%的税率征收契税。对个人购买家庭第二套改善性住房，面积为 90平方米及以下的，减按 1%的税率征收契税；面积为 90平方米以上的，减按 2%的税率征收契税。对个人购买家庭第三套及以上住房，一律按3%的税率征收契税。", 'eightF', 'w100');
        $section->addText("    2.2 个人转让房地产", 'eightF', 'w100');
        $section->addText("    2.2.1 个人将购买不足2年的住房对外销售的，按照5%的征收率全额缴纳增值税；个人将购买2年以上（含2年）的住房对外销售的，免征增值税。 ", 'eightF', 'w100');
        $section->addText("    2.2.2 城市建设维护税、教育费附加、地方教育费附加：随增值税附征。", 'eightF', 'w100');
        $section->addText("    2.2.3个人所得税：能提供完善、准确凭证的，据实征收，按转让收入减除房产原值及合理税费的余额的20%征收；否则，按转让收入1% 核定征收。个人转让自用5年以上，并且是家庭唯一生活用房，取得的所得免征个人所得税。", 'eightF', 'w100');
        $section->addText("    3、住房价格走势", 'eightF', 'w100');
        $section->addText("    2021年1月至2021年6月沛县房地产市场整体上涨，6月环比上月下降0.14%，同比去年同期上涨19.71%。本小区所处区域为沛县，龙润华府小区网上无挂牌信息，附近张庄新村住宅2021年06月（估价时点）挂牌均价3412元/㎡，较去年年初上升3.6%。待评估的龙润华府附近张庄新村二手房近六个月挂牌价格波动区间为3100-3400元/㎡。", 'eightF', 'w100');
        $textrun = $section->addTextRun();
        $textrun->addImage('http://blog.aaayun.cc/logo.png', ["width" => 500, 'height' => 180]);
        $textrun->addImage('http://blog.aaayun.cc/logo.png', ["width" => 500, 'height' => 180]);
        $section->addText("              沛县、张庄新村房价走势情况（安居客网站截图）",array('name' => '宋体', 'size' => 10, 'bold' => true, 'color' => '1B2232', 'align' => 'center'), 'w100');

        $section->addText("四、估价对象最高最佳利用分析",  array('name' => '宋体', 'size' => 15, 'bold' => true, 'color' => '1B2232', 'align' => 'center'), 'w100');
        $section->addText("    最高最佳利用原则要求估价结果是在估价对象依法最高最佳利用下的价值。所谓最高最佳利用是指法律上允许，技术上可能，经济上可行，经过充分合理的论证，使估价对象的价值最大的一种利用。", 'eightF', 'w100');
        $section->addText("    最高最佳利用包括了最佳的用途、最佳的集约和最佳的档次。 ", 'eightF', 'w100');
        $section->addText("五、估价方法适用性分析",  array('name' => '宋体', 'size' => 15, 'bold' => true, 'color' => '1B2232', 'align' => 'center'), 'w100');
        $section->addText("    估价人员在认真分析所掌握的资料并对邻近类似房地产进行实地勘查、调查后，根据估价对象的特点，遵照国家有关法律、法规、估价技术标准，经过研究，根据《房地产估价规范》（GB/T 50291-2015）4.1.2条款规定（估价方法选用章节内容），本宗房地产因类似房地产买卖实例较多，评估应优先采用比较法，且不适合采用成本法等其他方法，确定采用比较法进行评估。", 'eightF', 'w100');
        $section->addText("六、估价测算过程",  array('name' => '宋体', 'size' => 15, 'bold' => true, 'color' => '1B2232', 'align' => 'center'), 'w100');
        $section->addText("    （一）估价对象及可比实例因素条件说明一览表",array('name' => '宋体', 'size' => 15, 'bold' => true, 'color' => '1B2232', 'align' => 'center'), 'w100');



        $fancyTableStyleName = 'table2';
        $fancyTableStyle = array(
            'borderSize' => 1,
        );

        $fontStyle = array('bold'=>false, 'align'=>'center', 'size' => 11);//文字样式
        $cellColSpan = array('gridSpan' => 2, 'valign' => 'center'); // 单元格列合并
        $cellHCentered = array('alignment' => Jc::CENTER); // 水平居中

        $phpWord->addTableStyle($fancyTableStyleName, $fancyTableStyle, []);
        $table = $section->addTable($fancyTableStyleName);


        $baseCellStyle = ['borderSize'=>1,'vAlign'=>VerticalJc::CENTER,'vMerge'=>'restart'];//基础单元格属性，建议vMerge设置为restart，应该是重新开始一行的意思
        $colspan4 = array_merge(['gridSpan'=>4],$baseCellStyle);//跨4列单元格
        $colspan3 = array_merge(['gridSpan'=>3],$baseCellStyle);//跨3列单元格
        $colspan2 = array_merge(['gridSpan'=>2],$baseCellStyle);//跨2列单元格
        $rowSpan = array_merge($baseCellStyle,['vMerge'=>'continue']);//跨行单元格，注意与跨列设置数量不同
        $baseFontStyle = ['size'=>10,'lineHeight'=>1];
        $boldFontStyle = array_merge(['bold'=>true],$baseFontStyle);
        $titleFontStyle = ['size'=>12,'bold'=>true];

        $cell1 = 5000;
        $cell2 = 4000;
        $cell3 = 2000;

        $table->addRow();
        $table->addCell($cell1,$baseCellStyle)->addText('房屋',$boldFontStyle);//2500是宽度，没完全弄懂宽度的意思，请自行尝试
        $table->addCell($cell2,$baseCellStyle)->addText('估价对象',$boldFontStyle);//跨两列
        $table->addCell($cell3,$baseCellStyle)->addText('可比实例A',$boldFontStyle);
        $table->addCell($cell3,$baseCellStyle)->addText('可比实例B',$boldFontStyle);
        $table->addCell($cell3,$baseCellStyle)->addText('可比实例C',$boldFontStyle);
        $table->addCell($cell3,$baseCellStyle)->addText('可比实例D',$boldFontStyle);

        $table->addRow();
        $table->addCell($cell1,$baseCellStyle)->addText('项目',$boldFontStyle);
        $table->addCell($cell2, array('vMerge' => 'continue', 'gridSpan' => 1));
        $table->addCell($cell3*3,$colspan4)->addText('可比实例来源',$baseFontStyle);

        $table->addRow();
        $table->addCell($cell1, array('vMerge' => 'continue', 'gridSpan' => 1));
        $table->addCell($cell2, array('vMerge' => 'continue', 'gridSpan' => 1));
        $table->addCell($cell3,$baseCellStyle)->addText('存量房交易纳税可信数据',$baseFontStyle);
        $table->addCell($cell3,$baseCellStyle)->addText('商品房网签数据',$baseFontStyle);
        $table->addCell($cell3,$baseCellStyle)->addText('商品房网签数据',$baseFontStyle);
        $table->addCell($cell3,$baseCellStyle)->addText('商品房网签数据',$baseFontStyle);

        $table->addRow();
        $table->addCell($cell1,$baseCellStyle)->addText('证号类型',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('/',$baseFontStyle);
        $table->addCell($cell3,$baseCellStyle)->addText('税源编号',$baseFontStyle);
        $table->addCell($cell3,$baseCellStyle)->addText('不动产权证号',$baseFontStyle);
        $table->addCell($cell3,$baseCellStyle)->addText('不动产权证号',$baseFontStyle);
        $table->addCell($cell3,$baseCellStyle)->addText('不动产权证号',$baseFontStyle);

        $table->addRow();
        $table->addCell($cell1,$baseCellStyle)->addText('产权证号（或其他具有代表性编号）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('/',$baseFontStyle);
        $table->addCell($cell3,$baseCellStyle)->addText('F32032220210003645',$baseFontStyle);
        $table->addCell($cell3,$baseCellStyle)->addText('苏（2016）沛县不动产权第0006549号',$baseFontStyle);
        $table->addCell($cell3,$baseCellStyle)->addText('苏（2017）沛县不动产权第0002235号',$baseFontStyle);
        $table->addCell($cell3,$baseCellStyle)->addText('苏（2017）沛县不动产权第0001354号',$baseFontStyle);

        $table->addRow();
        for ($i=0;$i<6;$i++) {
            $table->addCell($cell1, array('vMerge' => 'continue', 'gridSpan' => 1));
        }

        $table->addRow();
        for ($i=0;$i<6;$i++) {
            $table->addCell($cell1, array('vMerge' => 'continue', 'gridSpan' => 1));
        }

        $table->addRow();
        $table->addCell($cell1,$baseCellStyle)->addText('建筑面积（㎡）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('99.36',$baseFontStyle);
        $table->addCell($cell3,$baseCellStyle)->addText('99.36',$baseFontStyle);
        $table->addCell($cell3,$baseCellStyle)->addText('99.36',$baseFontStyle);
        $table->addCell($cell3,$baseCellStyle)->addText('99.36',$baseFontStyle);
        $table->addCell($cell3,$baseCellStyle)->addText('99.36',$baseFontStyle);

        $table->addRow();
        $table->addCell($cell1,$baseCellStyle)->addText('结构',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('钢混',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('钢混',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('钢混',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('钢混',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('钢混',$baseFontStyle);

        $table->addRow();
        $table->addCell($cell1,$baseCellStyle)->addText('层次',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('1/6',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('1/6',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('1/6',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('1/6',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('1/6',$baseFontStyle);

        $table->addRow();
        $table->addCell($cell1,$baseCellStyle)->addText('朝向',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('二室朝南',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('二室朝南',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('二室朝南',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('二室朝南',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('二室朝南',$baseFontStyle);

        $table->addRow();
        $table->addCell($cell1,$baseCellStyle)->addText('建成时间',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('2016',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('2016',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('2016',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('2016',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('2016',$baseFontStyle);

        $table->addRow();
        $table->addCell($cell1,$baseCellStyle)->addText('东西至',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('东边户',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('东边户',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('东边户',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('东边户',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('东边户',$baseFontStyle);

        $table->addRow();
        $table->addCell($cell1,$baseCellStyle)->addText('装修等级',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('/',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('简单装修',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('简单装修',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('简单装修',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('简单装修',$baseFontStyle);

        $table->addRow();
        $table->addCell($cell1,$baseCellStyle)->addText('附属物情况',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('/',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('/',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('/',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('/',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('/',$baseFontStyle);

        $table->addRow();
        $table->addCell($cell1,$baseCellStyle)->addText('房屋所在区位（代码）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('00017',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('00017',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('00017',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('00017',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('00017',$baseFontStyle);

        $table->addRow();
        $table->addCell($cell1,$baseCellStyle)->addText('备注（房屋性质）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('商品房',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('商品房',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('商品房',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('商品房',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('商品房',$baseFontStyle);

        $table->addRow();
        $table->addCell($cell1,$baseCellStyle)->addText('区位因素1（自然环境）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('自然景观',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（8）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（8）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（8）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（8）',$baseFontStyle);

        $table->addRow();
        for ($i=0;$i<6;$i++) {
            $table->addCell($cell1, array('vMerge' => 'continue', 'gridSpan' => 1));
        }


        $table->addRow();
        $table->addCell($cell1, array('vMerge' => 'continue', 'gridSpan' => 1));
        $table->addCell($cell2,$baseCellStyle)->addText('环境污染',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（7）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（7）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（7）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（7）',$baseFontStyle);

        $table->addRow();
        $table->addCell($cell1,$baseCellStyle)->addText('区位因素2（规划设计）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('建筑或小区布局',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（5）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（5）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（5）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（5）',$baseFontStyle);

        $table->addRow();
        $table->addCell($cell1, array('vMerge' => 'continue', 'gridSpan' => 1));
        $table->addCell($cell2,$baseCellStyle)->addText('建筑密度、外型等',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（4）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（4）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（4）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（4）',$baseFontStyle);

        $table->addRow();
        $table->addCell($cell1, array('vMerge' => 'continue', 'gridSpan' => 1));
        $table->addCell($cell2,$baseCellStyle)->addText('室外公共活动空间与绿化景观',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（6）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（6）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（6）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（6）',$baseFontStyle);

        $table->addRow();
        $table->addCell($cell1,$baseCellStyle)->addText('区位因素3（物业管理）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('房屋、设备维修与管理',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（5）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（5）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（5）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（5）',$baseFontStyle);

        $table->addRow();
        $table->addCell($cell1, array('vMerge' => 'continue', 'gridSpan' => 1));
        $table->addCell($cell2,$baseCellStyle)->addText('小区治安及道路停车管理',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（5）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（5）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（5）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（5）',$baseFontStyle);

        $table->addRow();
        $table->addCell($cell1, array('vMerge' => 'continue', 'gridSpan' => 1));
        $table->addCell($cell2,$baseCellStyle)->addText('环卫与绿化管理',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（5）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（5）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（5）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（5）',$baseFontStyle);

        $table->addRow();
        $table->addCell($cell1,$baseCellStyle)->addText('区位因素4（交通条件）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('离市（县）区主干道的距离',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（10）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（10）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（10）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（10）',$baseFontStyle);

        $table->addRow();
        $table->addCell($cell1, array('vMerge' => 'continue', 'gridSpan' => 1));
        $table->addCell($cell2,$baseCellStyle)->addText('公交线路情况',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（10）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（10）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（10）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（10）',$baseFontStyle);

        $table->addRow();
        $table->addCell($cell1,$baseCellStyle)->addText('区位因素5（教育医疗等设施）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('所在学区学校情况',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（5）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（5）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（5）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（5）',$baseFontStyle);

        $table->addRow();
        $table->addCell($cell1, array('vMerge' => 'continue', 'gridSpan' => 1));
        $table->addCell($cell2,$baseCellStyle)->addText('医院及医疗机构分布情况',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（5）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（5）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（5）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（5）',$baseFontStyle);

        $table->addRow();
        $table->addCell($cell1, array('vMerge' => 'continue', 'gridSpan' => 1));
        $table->addCell($cell2,$baseCellStyle)->addText('文体娱乐设施',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（5）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（5）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（5）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（5）',$baseFontStyle);

        $table->addRow();
        $table->addCell($cell1,$baseCellStyle)->addText('区位因素6（商业配套）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('重要商业配套设施',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（8）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（8）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（8）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（8）',$baseFontStyle);

        $table->addRow();
        $table->addCell($cell1, array('vMerge' => 'continue', 'gridSpan' => 1));
        $table->addCell($cell2,$baseCellStyle)->addText('菜场超市',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（12）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（12）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（12）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('三等（12）',$baseFontStyle);

        $table->addRow();
        $table->addCell($cell2,$baseCellStyle)->addText('交易时间',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('2021.6.1',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('2021.5.12',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('2021.3.16',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('2021.5.2',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('2021.4.15',$baseFontStyle);

        $table->addRow();
        $table->addCell($cell2,$baseCellStyle)->addText('交易单价（元/㎡）',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('/',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('2990',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('2660',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('2710',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('2530',$baseFontStyle);

        $table->addRow();
        $table->addCell($cell2,$baseCellStyle)->addText('对应时点本小区二手房挂牌均价',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('3412',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('3437',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('3370',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('3437',$baseFontStyle);
        $table->addCell($cell2,$baseCellStyle)->addText('3370',$baseFontStyle);

        $section->addText("（二）修正系数应用说明",array('name' => '宋体', 'size' => 15, 'bold' => true, 'color' => '1B2232', 'align' => 'center'), 'w100');
        $section->addText("    1、交易情况修正", 'eightF', 'w100');
        $section->addText("    由于采用买卖的正常交易实例为可比实例，因此该系数取 100，代入公式计算数值为1.000。", 'eightF', 'w100');
        $section->addText("    2、交易日期调整", 'eightF', 'w100');
        $section->addText("    方法一：价格指数法。根据房产管理部门提供的本地区对应物业类型的月度价格指数计算确定调整值；", 'eightF', 'w100');
        $section->addText("    方法二：小区挂牌价法。根据房地产信息网站提供的同一小区（商圈）对应物业类型的月度挂牌均价计算确定调整值。本方法使用限定条件为所有可比实例和待估房产均在同一小区（商圈）；", 'eightF', 'w100');
        $section->addText("    方法三：片区挂牌价法。根据房地产信息网站提供的同一区县对应物业类型的月度挂牌均价计算确定调整值。本方法使用限定条件为所有可比实例和待估房产均在同一区县；", 'eightF', 'w100');

        $textrun = $section->addTextRun();
        $textrun->addImage(PUB . '/image/s2.png', ["width" => 450, 'height' => 50]);
        $section->addText("    本估价项目采用方法二，采集了可比实例对应月度小区的普通多层物业类型的挂牌均价，交易日期修正计算表如下：", 'eightF', 'w100');

        $fancyTableStyleName = 'table4';
        $fancyTableStyle = array(
            'borderSize' => 1,
        );

        $fontStyle = array('bold'=>false, 'align'=>'center', 'size' => 11);//文字样式

        $phpWord->addTableStyle($fancyTableStyleName, $fancyTableStyle, []);
        $table = $section->addTable($fancyTableStyleName);

        $table->addRow(200);
        $table->addCell(2500)->addText('内容',$fontStyle);
        $table->addCell(2500)->addText('标准房',$fontStyle);
        $table->addCell(2500)->addText('可比实例A',$fontStyle);
        $table->addCell(2500)->addText('可比实例B',$fontStyle);
        $table->addCell(2500)->addText('可比实例C',$fontStyle);
        $table->addCell(2500)->addText('可比实例D',$fontStyle);


        $row = $table->addRow();
        $table->addCell(2200)->addText('交易时间（估价时点）',$fontStyle);
        $table->addCell(2200)->addText('2021.6.1',$fontStyle);
        $table->addCell(2200)->addText('2021.5.12',$fontStyle);
        $table->addCell(2200)->addText('2021.3.16',$fontStyle);
        $table->addCell(2200)->addText('2021.5.2',$fontStyle);
        $table->addCell(2200)->addText('2021.4.15',$fontStyle);


        $row = $table->addRow();
        $table->addCell(2200)->addText('交易单价 (元/㎡)',$fontStyle);
        $table->addCell(2200)->addText('/',$fontStyle);
        $table->addCell(2200)->addText('2990',$fontStyle);
        $table->addCell(2200)->addText('2660',$fontStyle);
        $table->addCell(2200)->addText('2710',$fontStyle);
        $table->addCell(2200)->addText('2530',$fontStyle);

        $row = $table->addRow();
        $table->addCell(2200)->addText('对应时点二手房均价',$fontStyle);
        $table->addCell(2200)->addText('3412',$fontStyle);
        $table->addCell(2200)->addText('3437',$fontStyle);
        $table->addCell(2200)->addText('3370',$fontStyle);
        $table->addCell(2200)->addText('3437',$fontStyle);
        $table->addCell(2200)->addText('3370',$fontStyle);

        $row = $table->addRow();
        $table->addCell(2200)->addText('交易日期修正',$fontStyle);
        $table->addCell(2200)->addText('3412/3412',$fontStyle);
        $table->addCell(2200)->addText('3412/3437',$fontStyle);
        $table->addCell(2200)->addText('3412/3370',$fontStyle);
        $table->addCell(2200)->addText('3412/3437',$fontStyle);
        $table->addCell(2200)->addText('3412/3370',$fontStyle);

        $row = $table->addRow();
        $table->addCell(2200)->addText('交易日期修正值',$fontStyle);
        $table->addCell(2200)->addText('1',$fontStyle);
        $table->addCell(2200)->addText('0.9927',$fontStyle);
        $table->addCell(2200)->addText('1.0125',$fontStyle);
        $table->addCell(2200)->addText('0.9927',$fontStyle);
        $table->addCell(2200)->addText('1.0125',$fontStyle);

        $section->addText("    本报告标准房的评估价值时点为2021年06月01日，标准房附近相似物业类型小区对应时点的6月二手房挂牌均价为3412元/㎡。", 'eightF', 'w100');


        $fancyTableStyleName = 'table5';
        $fancyTableStyle = array(
            'borderSize' => 1,
        );

        $fontStyle = array('bold'=>false, 'align'=>'center', 'size' => 11);//文字样式

        $phpWord->addTableStyle($fancyTableStyleName, $fancyTableStyle, []);
        $table = $section->addTable($fancyTableStyleName);

        $table->addRow(200);
        $table->addCell(2500)->addText('标准房',$fontStyle);
        $table->addCell(2500)->addImage('http://blog.aaayun.cc/logo.png', ["width" => 450, 'height' => 150]);
        $table->addRow(200);
        $table->addCell(2500)->addText('可比实例A',$fontStyle);
        $table->addCell(2500)->addImage('http://blog.aaayun.cc/logo.png', ["width" => 450, 'height' => 150]);
        $table->addRow(200);
        $table->addCell(2500)->addText('可比实例B',$fontStyle);
        $table->addCell(2500)->addImage('http://blog.aaayun.cc/logo.png', ["width" => 450, 'height' => 150]);
        $table->addRow(200);
        $table->addCell(2500)->addText('可比实例C',$fontStyle);
        $table->addCell(2500)->addImage('http://blog.aaayun.cc/logo.png', ["width" => 450, 'height' => 150]);
        $table->addRow(200);
        $table->addCell(2500)->addText('可比实例D',$fontStyle);
        $table->addCell(2500)->addImage('http://blog.aaayun.cc/logo.png', ["width" => 450, 'height' => 150]);

        $section->addText("    3、实体因素修正", 'eightF', 'w100');
        $section->addText("    （1）住宅房屋层次因素系数表", 'eightF', 'w100');

        $fancyTableStyleName = 'table5';
        $fancyTableStyle = array(
            'borderSize' => 1,
        );

        $fontStyle = array('bold'=>false, 'align'=>'center', 'size' => 11);//文字样式

        $phpWord->addTableStyle($fancyTableStyleName, $fancyTableStyle, []);
        $table = $section->addTable($fancyTableStyleName);

        $table->addRow(200);
        $table->addCell(2500)->addText('总层数',$fontStyle);
        $table->addCell(2500)->addText('二层楼',$fontStyle);
        $table->addCell(2500)->addText('三层楼',$fontStyle);
        $table->addCell(2500)->addText('四层楼',$fontStyle);
        $table->addCell(2500)->addText('五层楼',$fontStyle);
        $table->addCell(2500)->addText('六层楼',$fontStyle);
        $table->addCell(2500)->addText('七层楼',$fontStyle);

        $table->addRow(200);
        $table->addCell(2500)->addText('一',$fontStyle);
        $table->addCell(2500)->addText('100',$fontStyle);
        $table->addCell(2500)->addText('100',$fontStyle);
        $table->addCell(2500)->addText('100',$fontStyle);
        $table->addCell(2500)->addText('100',$fontStyle);
        $table->addCell(2500)->addText('100',$fontStyle);
        $table->addCell(2500)->addText('100',$fontStyle);

        $table->addRow(200);
        $table->addCell(2500)->addText('二',$fontStyle);
        $table->addCell(2500)->addText('94',$fontStyle);
        $table->addCell(2500)->addText('96',$fontStyle);
        $table->addCell(2500)->addText('96',$fontStyle);
        $table->addCell(2500)->addText('96',$fontStyle);
        $table->addCell(2500)->addText('96',$fontStyle);
        $table->addCell(2500)->addText('96',$fontStyle);

        $table->addRow(200);
        $table->addCell(2500)->addText('三',$fontStyle);
        $table->addCell(2500)->addText('/',$fontStyle);
        $table->addCell(2500)->addText('92',$fontStyle);
        $table->addCell(2500)->addText('94',$fontStyle);
        $table->addCell(2500)->addText('94',$fontStyle);
        $table->addCell(2500)->addText('94',$fontStyle);
        $table->addCell(2500)->addText('94',$fontStyle);

        $table->addRow(200);
        $table->addCell(2500)->addText('四',$fontStyle);
        $table->addCell(2500)->addText('/',$fontStyle);
        $table->addCell(2500)->addText('/',$fontStyle);
        $table->addCell(2500)->addText('88',$fontStyle);
        $table->addCell(2500)->addText('92',$fontStyle);
        $table->addCell(2500)->addText('92',$fontStyle);
        $table->addCell(2500)->addText('92',$fontStyle);

        $table->addRow(200);
        $table->addCell(2500)->addText('五',$fontStyle);
        $table->addCell(2500)->addText('/',$fontStyle);
        $table->addCell(2500)->addText('/',$fontStyle);
        $table->addCell(2500)->addText('/',$fontStyle);
        $table->addCell(2500)->addText('85',$fontStyle);
        $table->addCell(2500)->addText('88',$fontStyle);
        $table->addCell(2500)->addText('88',$fontStyle);

        $table->addRow(200);
        $table->addCell(2500)->addText('六',$fontStyle);
        $table->addCell(2500)->addText('/',$fontStyle);
        $table->addCell(2500)->addText('/',$fontStyle);
        $table->addCell(2500)->addText('/',$fontStyle);
        $table->addCell(2500)->addText('/',$fontStyle);
        $table->addCell(2500)->addText('82',$fontStyle);
        $table->addCell(2500)->addText('85',$fontStyle);

        $table->addRow(200);
        $table->addCell(2500)->addText('七',$fontStyle);
        $table->addCell(2500)->addText('/',$fontStyle);
        $table->addCell(2500)->addText('/',$fontStyle);
        $table->addCell(2500)->addText('/',$fontStyle);
        $table->addCell(2500)->addText('/',$fontStyle);
        $table->addCell(2500)->addText('/',$fontStyle);
        $table->addCell(2500)->addText('78',$fontStyle);

        $section->addText("    （2）住宅结构因素修正系数表", 'eightF', 'w100');
        $fancyTableStyleName = 'table6';
        $fancyTableStyle = array(
            'borderSize' => 1,
        );

        $fontStyle = array('bold'=>false, 'align'=>'center', 'size' => 11);//文字样式

        $phpWord->addTableStyle($fancyTableStyleName, $fancyTableStyle, []);
        $table = $section->addTable($fancyTableStyleName);

        $table->addRow(200);
        $table->addCell(2500)->addText('结构类型',$fontStyle);
        $table->addCell(2500)->addText('钢混结构',$fontStyle);
        $table->addCell(2500)->addText('砖混结构',$fontStyle);
        $table->addCell(2500)->addText('砖木结构',$fontStyle);

        $table->addRow(200);
        $table->addCell(2500)->addText('系数取值',$fontStyle);
        $table->addCell(2500)->addText('100',$fontStyle);
        $table->addCell(2500)->addText('100',$fontStyle);
        $table->addCell(2500)->addText('90',$fontStyle);

        $section->addText("    （3）住宅朝向因素修正系数表", 'eightF', 'w100');
        $fancyTableStyleName = 'table7';
        $fancyTableStyle = array(
            'borderSize' => 1,
        );

        $fontStyle = array('bold'=>false, 'align'=>'center', 'size' => 11);//文字样式

        $phpWord->addTableStyle($fancyTableStyleName, $fancyTableStyle, []);
        $table = $section->addTable($fancyTableStyleName);

        $table->addRow(200);
        $table->addCell(2500)->addText('卧室朝向',$fontStyle);
        $table->addCell(2500)->addText('北',$fontStyle);
        $table->addCell(2500)->addText('西',$fontStyle);
        $table->addCell(2500)->addText('东',$fontStyle);
        $table->addCell(2500)->addText('一间朝南',$fontStyle);
        $table->addCell(2500)->addText('两间朝南',$fontStyle);
        $table->addCell(2500)->addText('三间朝南',$fontStyle);

        $table->addRow(200);
        $table->addCell(2500)->addText('修正系数',$fontStyle);
        $table->addCell(2500)->addText('86',$fontStyle);
        $table->addCell(2500)->addText('90',$fontStyle);
        $table->addCell(2500)->addText('95',$fontStyle);
        $table->addCell(2500)->addText('98',$fontStyle);
        $table->addCell(2500)->addText('100',$fontStyle);
        $table->addCell(2500)->addText('102',$fontStyle);

        $section->addText("    （4）住宅装修情况因素修正系数表", 'eightF', 'w100');

        $fancyTableStyleName = 'table8';
        $fancyTableStyle = array(
            'borderSize' => 1,
        );

        $fontStyle = array('bold'=>false, 'align'=>'center', 'size' => 11);//文字样式

        $phpWord->addTableStyle($fancyTableStyleName, $fancyTableStyle, []);
        $table = $section->addTable($fancyTableStyleName);

        $table->addRow(200);
        $table->addCell(2500)->addText('装修等级',$fontStyle);
        $table->addCell(2500)->addText('毛坯',$fontStyle);
        $table->addCell(2500)->addText('简单装修',$fontStyle);
        $table->addCell(2500)->addText('中档装修',$fontStyle);
        $table->addCell(2500)->addText('高档装修',$fontStyle);

        $table->addRow(200);
        $table->addCell(2500)->addText('单位面积价格',$fontStyle);
        $table->addCell(2500)->addText('0',$fontStyle);
        $table->addCell(2500)->addText('280',$fontStyle);
        $table->addCell(2500)->addText('580',$fontStyle);
        $table->addCell(2500)->addText('1150',$fontStyle);

        $textrun = $section->addTextRun();
        $textrun->addImage(PUB . '/image/s3.png', ["width" => 450, 'height' => 30]);

        $section->addText("    （5）住宅成新因素修正系数表", 'eightF', 'w100');
        $section->addText("        以估价对象所对应的房屋建成年代为基准（100%），相差每相差一年修正±0.5%。", 'eightF', 'w100');
        $section->addText("    （6）附属物计价系数", 'eightF', 'w100');
        $fancyTableStyleName = 'table9';
        $fancyTableStyle = array(
            'borderSize' => 1,
        );

        $fontStyle = array('bold'=>false, 'align'=>'center', 'size' => 11);//文字样式

        $phpWord->addTableStyle($fancyTableStyleName, $fancyTableStyle, []);
        $table = $section->addTable($fancyTableStyleName);

        $table->addRow(200);
        $table->addCell(2500)->addText('附属物',$fontStyle);
        $table->addCell(2500)->addText('车库',$fontStyle);
        $table->addCell(2500)->addText('阁楼',$fontStyle);
        $table->addCell(2500)->addText('储藏室',$fontStyle);

        $table->addRow(200);
        $table->addCell(2500)->addText('系数取值',$fontStyle);
        $table->addCell(2500)->addText('60',$fontStyle);
        $table->addCell(2500)->addText('50',$fontStyle);
        $table->addCell(2500)->addText('35',$fontStyle);

        $section->addText("    （7）区位因素调节系数评分等级说明表", 'eightF', 'w100');

        $textrun = $section->addTextRun();
        $textrun->addImage(PUB . '/image/table2.png', ["width" => 450, 'height' => 550]);
        $section->addText("    （8）区位因素修正调节系数评分表", 'eightF', 'w100');
        $section->addText("七、估价结果确定",  array('name' => '宋体', 'size' => 15, 'bold' => true, 'color' => '1B2232', 'align' => 'center'), 'w100');
        $section->addText("    将上述四个比准价格的简单算术平均数作为市场比较法的测算结果。 ", 'eightF', 'w100');
        $section->addText("    标准房基准价格=(2854+2690+2624+2673)/4=2710元/㎡（取整） ", 'eightF', 'w100');




        /*

         */

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save('helloWorld.docx');
    }

    // 第一页
    public function set1($section, $phpWord, $spc, $data) {

        $phpWord->addParagraphStyle(
            'title',[
                'spaceBefore' => 0,
                'spaceAfter' => 0,
                'lineHeight' => 1.0,  // 行间距
                'spacing' => 240,
                'align' => 'center'
            ]
        );

        $section->addText(
            '<w:br /><w:br /><w:br /><w:br />', $spc
        );
        $phpWord->addFontStyle(
            'oneUserDefinedStyle',
            array('name' => '华文中宋', 'size' => 42, 'color' => '1B2232', 'bold' => true, 'align' => 'right')
        );
        $section->addText(
            '房地产估价报告',
            'oneUserDefinedStyle',
            'title'
        );

        $fontStyle = new \PhpOffice\PhpWord\Style\Font();
        $fontStyle->setName('宋体');
        $fontStyle->setSize(14);
        $myTextElement = $section->addText(
            '估价报告编号：（江苏）苏北（2021年）（评税沛）第028号<w:br />' .
            '估价项目名称：沛县龙润华府小区 普通多层 住宅标准房基准价格评估<w:br />' .
            '估价委托人：沛县价格认定中心<w:br />' .
            '房地产估价机构：江苏苏北土地房地产资产评估测绘咨询有限公司<w:br />' .
            '估价人员：国家注册房地产估价师：周  擎  注册证号： 3220210187<w:br />' .
            '　　　　 国家注册房地产估价师：汪雪娟   注册证号：3220030108<w:br />' .
            '　　　　 房地产估价助理：周擎<w:br />' .
            '估价报告出具日期：二〇二一年六月六日<w:br />'
        );
        $myTextElement->setFontStyle($fontStyle);
    }

    public function set2($section, $phpWord, $spc, $data)
    {
        $section->addPageBreak();

        $section->addText(
            '目    录',
            'three',
            [
                'spaceBefore' => 0,
                'spaceAfter' => 0,
                'lineHeight' => 1.0,  // 行间距
                'spacing' => 240,
                'align' => 'center'
            ]
        );

        $textrun = $section->addTextRun();
        $textrun->addImage(PUB . 'image/sy.png', ["width" => 470, 'height' => 520]);
    }

    public function set3($section, $phpWord, $spc, $data) {
        $phpWord->addFontStyle(
            'mulu3',
            array('name' => '宋体', 'size' => 14, 'color' => '1B2232', 'align' => 'center')
        );
        $section->addPageBreak();
        $phpWord->addFontStyle(
            'three',
            array('name' => '宋体', 'size' => 22, 'color' => '1B2232', 'bold' => true, 'align' => 'center')
        );
        $section->addText(
            '致估价委托人函',
            'three',
            [
                'spaceBefore' => 0,
                'spaceAfter' => 0,
                'lineHeight' => 1.0,  // 行间距
                'spacing' => 240,
                'align' => 'center'
            ]
        );
        $section->addText('1. 致函对象', 'mulu3');
        $section->addText('    沛县价格认定中心', 'mulu3');
        $section->addText('2．估价目的', 'mulu3');
        $section->addText('    评估其市场价值，为征收机关核定计税依据提供房地产价值参考依据。', 'mulu3');
        $section->addText('3．估价对象', 'mulu3');
        $section->addText('    估价对象位于沛县范围内的龙润华府小区普通多层存量房。该小区内物业类型包括：普通多层、小高层。根据估价目的，依据《江苏省存量房交易纳税评估系统业务规程》（2020年11月修订版），结合估价人员现场查勘，选取了龙润华府小区4-1-101室为该小区钢混结构普通多层的标准样本房，所在楼层1层，房屋总层数6层，建筑面积为99.36㎡，南北朝向，东边户，钢混结构，设计用途为住宅，建于2016年。 土地使用权类型为出让，分摊土地使用权面积12.0平方米，土地使用权终止日期为2083年。', 'mulu3');
        $section->addText('4．价值时点', 'mulu3');
        $section->addText('    2021年06月01日。', 'mulu3');
        $section->addText('5. 价值类型', 'mulu3');
        $section->addText('    本次估价采用公开市场价值标准，即本报告评估估价对象在目前情况下，于价值时点的市场价值，是未设定法定优先受偿权利下的市场价值。 ', 'mulu3');
        $section->addText('6．估价方法', 'mulu3');
    }

    public function set4($section, $phpWord, $spc, $data) {
        $phpWord->addFontStyle(
            'mulu3',
            array('name' => '宋体', 'size' => 14, 'color' => '1B2232', 'align' => 'center')
        );
        $section->addText('    比较法', 'mulu3');
        $section->addText('7．估价结果', 'mulu3');
        $section->addText('    在价值时点 2021年06月01日 ，估价对象于价值时点的参考价为人民币单价：每平方米贰仟柒佰壹拾元整（RMB2710元/平方米）。本估价结果是估价对象在价值时点未设立法定优先受偿权下的市场价值。 ', 'mulu3');

        $section->addText('8. 特别提示', 'mulu3');
        $section->addText('    1.估价对象状况和房地产市场状况因时间变化对房地产市场价值可能产生影响。在估价对象保持正常维护使用、区位因素未发生变化、近期房地产市场政策性调控目的在稳定房价的前提下，在报告有效期内，估价对象价值保持相对稳定。', 'mulu3');
        $section->addText('    2.估价报告使用者要合理使用评估价值，充分考虑处置房地产时的急变现价值减损和相关费用的影响。', 'mulu3');
        $section->addText('    3.定期或者在房地产市场价格变化较快时对房地产市场价值进行再评估。', 'mulu3');
        $section->addText('9. 致函日期', 'mulu3');
        $section->addText('    2021年06月06日', 'mulu3');
        $section->addText('    详情请参阅估价报告全文！', 'mulu3');
        $section->addText('                         法定代表人（盖章）：', 'mulu3');
        $section->addTextBreak();
        $section->addText('江苏苏北土地房地产资产评估测绘咨询有限公司', 'mulu3', ['align' => 'right']);
        $section->addText('二〇二一年六月六日', 'mulu3', ['align' => 'right']);
    }

    public function set5($section, $phpWord, $spc, $data) {
        $phpWord->addParagraphStyle(
            'w',array(
                'spacing' => 50,
            )
        );
        $phpWord->addFontStyle(
            'three',
            array('name' => '宋体', 'size' => 22, 'color' => '1B2232', 'bold' => true, 'align' => 'center')
        );
        $section->addText(
            '估 价 师 声 明',
            'three',
            [
                'spaceBefore' => 0,
                'spaceAfter' => 0,
                'lineHeight' => 1.0,  // 行间距
                'spacing' => 240,
                'align' => 'center'
            ]
        );



        $section->addText("我们郑重声明：", array('name' => '宋体', 'size' => 15, 'bold' => true, 'color' => '1B2232', 'align' => 'center'), 'w');
        $fontStyle = new \PhpOffice\PhpWord\Style\Font();
        $fontStyle->setName('宋体');
        $fontStyle->setSize(13.5);
        $myTextElement = $section->addText(
            '1. 我们在本估价报告中陈述的事实是真实的和准确的。<w:br />' .
            '2. 本估价报告中的分析、意见和结论是我们自己公正的专业分析、意见和结论，但受到本估价报告中已说明的假设和限制条件的限制。<w:br />' .
            '3. 我们与本估价报告中的估价对象没有利害关系，也与有关当事人没有个人利害关系或偏见。<w:br />' .
            '4. 我们依据中华人民共和国国家标准《房地产估价规范》进行分析，形成意见和结论，撰写本估价报告。<w:br />' .
            '5. 我们已对本估价报告中的估价对象进行了实地勘察，并对勘察的客观性、真实性、公正性承担责任，但我们对估价对象的现场勘察仅限于其外观和使用状况，对被遮盖、未暴露及难以接触到的部分，依据了委托方提供的资料进行评估，除非另有协议，我们不承担对估价对象建筑结构质量进行调查的责任。<w:br />' .
            '6. 没有人对本估价报告提供过重要的专业帮助。<w:br />' .
            '7. 本估价报告依据了委托方提供的相关资料，委托方对资料的真实性负责。因资料失实造成估价结果有误的，估价机构和估价人员不承担相应的责任。<w:br />'.
            '8. 本报告估价结果仅作为委托方在本次估价目的下使用，不得做其他用途。经本估价机构和估价人员同意，估价报告不得向委托方及报告审查部门以外的单位及个人提供，凡因委托人使用估价报告不当而引起的后果，估价机构和估价人员不承担相应的责任。<w:br />' .
            '9. 本估价报告的全部或其部分内容不得发表于任何公开媒体上，报告解释权为本估价机构所有。<w:br />' .
            '参加本次评估的注册房地产估价师<w:br />'
        );
        $myTextElement->setFontStyle($fontStyle);
        $myTextElement->setParagraphStyle('w');

        $styleTable = array('borderSize'=>6, 'borderColor'=>'000000', 'cellMargin'=>80);//表格样式
        $fontStyle = array('bold'=>false, 'align'=>'center', 'size' => 11);//文字样式

        $phpWord->addTableStyle('table_1', $styleTable);//定义表格样式
        $table = $section->addTable('table_1');

        $table->addRow(200);
        $table->addCell(2200)->addText('姓名',$fontStyle);
        $table->addCell(2200)->addText('注册号',$fontStyle);
        $table->addCell(2200)->addText('签名',$fontStyle);
        $table->addCell(2200)->addText('签名日期',$fontStyle);

        $table->addRow(200);
        $table->addCell(2200)->addText('周  擎',$fontStyle);
        $table->addCell(2200)->addText('3220210187',$fontStyle);
        $table->addCell(2200)->addText('',$fontStyle);
        $table->addCell(2200)->addText('2021年06月06日',$fontStyle);

        $table->addRow(200);
        $table->addCell(2200)->addText('汪雪娟',$fontStyle);
        $table->addCell(2200)->addText('3220030108',$fontStyle);
        $table->addCell(2200)->addText('',$fontStyle);
        $table->addCell(2200)->addText('2021年06月06日',$fontStyle);
    }

    public function set6and7($section, $phpWord, $spc, $data) {
        $section->addPageBreak();
        $phpWord->addFontStyle(
            'three',
            array('name' => '宋体', 'size' => 22, 'color' => '1B2232', 'bold' => true, 'align' => 'center')
        );
        $section->addText(
            '估价的假设和限制条件',
            'three',
            [
                'spaceBefore' => 0,
                'spaceAfter' => 0,
                'lineHeight' => 1.0,  // 行间距
                'spacing' => 240,
                'align' => 'center'
            ]
        );

        $section->addText("本次评估依据如下假设：", array('name' => '宋体', 'size' => 13.5, 'bold' => true, 'color' => '1B2232', 'align' => 'center'), 'w100');
        $phpWord->addFontStyle(
            'mulu4',
            array('name' => '宋体', 'size' => 13.5, 'color' => '1B2232', 'align' => 'center')
        );

        $phpWord->addParagraphStyle(
            'w100',array(
                'spacing' => 100,
            )
        );

        $section->addText('    1. 一般假设', 'mulu4', 'w100');
        $section->addText('    ①估价对象产权清晰，手续齐全，可在公开市场上自由转让。', 'mulu4', 'w100');
        $section->addText('    ②市场供应关系、市场结构保持稳定，未发生重大变化或实质性改变。 ', 'mulu4', 'w100');
        $section->addText('    ③本次估价对估价委托人提供的估价对象的权属、面积、用途等资料进行了审慎检查，未予以核实其合法性、真实性、准确性和完整性的情况下，合理假设其合法、真实、准确和完整。', 'mulu4', 'w100');
        $section->addText('    ④本次估价对估价对象房屋安全、环境污染等影响估价对象价值的重大因素给予了关注，估价对象是否存在安全隐患且无相应的专业机构进行鉴定、检测的情况下，合理假设其安全。', 'mulu4', 'w100');
        $section->addText('    ⑤交易双方都具有完全市场信息，对交易对象具有必要的专业知识，不考虑特殊买家的附加出价。', 'mulu4', 'w100');
        $section->addText('    2. 未定事项假设', 'mulu4', 'w100');
        $section->addText('    ①估价时没有考虑国家宏观经济政策发生变化、市场供求关系变化、市场结构转变、遇有自然力和其他不可抗力等因素对房地产价值的影响，也没有考虑估价对象将来承担违约责任的事宜，以及特殊交易方式下的特殊交易价格等对评估价值的影响。当上述条件发生变化时，评估结果一般亦会发生变化。', 'mulu4', 'w100');
        $section->addText('    ②本估价是以提供给估价机构的估价对象不存在抵押权、典权等他项权利为假设前提。特提请报告使用人注意！', 'mulu4', 'w100');
        $section->addText('    3.背离事实假设', 'mulu4', 'w100');
        $section->addText('    ①估价对象未考虑未来处置风险，未考虑估价对象及其所有权人已承担的债务、或有债务及经营决策失误或市场动作失当对其价值的影响。', 'mulu4', 'w100');
        $section->addText('    ②特色装饰装修对受让人来说一般没有价值，故本次估价未考虑估价对象室内的特色装饰装修价值，且委托人未提供房地产附属物的合法证明，因此亦未考虑房地产附属物价值。 ', 'mulu4', 'w100');
        $section->addText('    4.不相一致假设', 'mulu4', 'w100');
        $section->addText('    本次估价对象的实际用途是住宅用地，产权证登记类型是住宅用地。所以本次估价不存在不相一致假设。', 'mulu4', 'w100');
        $section->addText('    5.依据不足假设', 'mulu4', 'w100');
        $section->addText('    由于委托方不能提供《房屋所有权证》原件供估价人员核查，本次估价假设估价对象与所描述一致。', 'mulu4', 'w100');
        $section->addText("本报告使用的限制条件： ", array('name' => '宋体', 'size' => 13.5, 'bold' => true, 'color' => '1B2232', 'align' => 'center'), 'w100');
        $section->addText('    1. 本次估价结果为满足全部假设条件和限制条件下的价值。 ', 'mulu4', 'w100');
        $section->addText('    2. 本报告及估价结果仅作为委托方了解本宗房地产基准价格时之参考依据，不得做其他用途。 ', 'mulu4', 'w100');
        $section->addText('    3. 本次估价结果是估价对象在价值时点的市场价值。价值时点后，估价报告有效期内估价对象的质量及价格标准发生变化，并对估价对象估价价值产生明显影响时，不能直接使用本估价结论。 ', 'mulu4', 'w100');
        $section->addText('    4. 本次估价结果未考虑委估标的强制变现及特殊交易方式的影响因素，未考虑国家宏观经济政策发生变化以及遇有自然灾害和其它不可抗力的影响因素。 ', 'mulu4', 'w100');
        $section->addText('    5. 本报告作为整体使用时有效。本报告有效期为一年，从 2021年06月06日 至2022年06月05日，超过有效期使用，本公司不承担任何责任。', 'mulu4', 'w100');

    }

    public function set8($section, $phpWord, $spc, $data) {
        $section->addPageBreak();
        $phpWord->addFontStyle(
            'three',
            array('name' => '宋体', 'size' => 22, 'color' => '1B2232', 'bold' => true, 'align' => 'center')
        );
        $phpWord->addFontStyle(
            'eightF',
            array('name' => '宋体', 'size' => 14, 'bold' => false, 'color' => '1B2232', 'align' => 'center')
        );
        $phpWord->addFontStyle(
            'eightFG',
            array('name' => '宋体', 'size' => 9, 'bold' => false, 'color' => '1B2232', 'align' => 'center')
        );
        $section->addText(
            '房地产估价结果报告',
            'three',
            [
                'spaceBefore' => 0,
                'spaceAfter' => 0,
                'lineHeight' => 1.0,  // 行间距
                'spacing' => 240,
                'align' => 'center'
            ]
        );

        $section->addText("一、估价委托人", array('name' => '宋体', 'size' => 15, 'bold' => true, 'color' => '1B2232', 'align' => 'center'), 'w100');
        $section->addText("    名称： 沛县价格认定中心", 'eightF', 'w100');
        $section->addText("二、房地产估价机构", array('name' => '宋体', 'size' => 15, 'bold' => true, 'color' => '1B2232', 'align' => 'center'), 'w100');
        $section->addText("    名称：江苏苏北土地房地产资产评估测绘咨询有限公司", 'eightF', 'w100');
        $section->addText("    地址：徐州市三环南路39号商办楼1-702室", 'eightF', 'w100');
        $section->addText("    统一社会信用代码：91320311744804482L", 'eightF', 'w100');
        $section->addText("    法定代表人：吴伟", 'eightF', 'w100');
        $section->addText("    备案等级：壹级", 'eightF', 'w100');
        $section->addText("    证书编号：苏建房估备（壹）徐州00020", 'eightF', 'w100');
        $section->addText("    联系电话：0516-85793968", 'eightF', 'w100');
        $section->addText("三、估价目的", array('name' => '宋体', 'size' => 15, 'bold' => true, 'color' => '1B2232', 'align' => 'center'), 'w100');
        $section->addText("    评估其市场价值，为征收机关核定计税依据提供房地产价值参考依据。", 'eightF', 'w100');

        $section->addText("四、估价对象", array('name' => '宋体', 'size' => 15, 'bold' => true, 'color' => '1B2232', 'align' => 'center'), 'w100');
        $section->addText("    1.估价对象基本状况 ", 'eightF', 'w100');
        $section->addText("    1.1 名称、坐落、四至", 'eightF', 'w100');
        $section->addText("    估价对象为沛县龙润华府小区4-1-101室，坐落于沛县乡镇评税片区，运河路北侧住宅区内。估价对象东至S253，南至运河路，西至张庄镇人民政府，北至空地。 ", 'eightF', 'w100');
        $section->addText("    1.2 范围 ", 'eightF', 'w100');
        $section->addText("    估价对象为位于沛县龙润华府小区4-1-101室住宅房地产，包含建筑物价值和分摊土地使用权价值，不含估价对象的特色装饰装修及附属物价值。 ", 'eightF', 'w100');
        $section->addText("    1.3 规模 ", 'eightF', 'w100');
        $section->addText("    估价对象房屋建筑面积为99.36㎡。 ", 'eightF', 'w100');
        $section->addText("    1.4 用途 ", 'eightF', 'w100');
        $section->addText("    估价对象现状用途为国有出让住宅用地上的住宅用途房地产。", 'eightF', 'w100');
        $section->addText("    2、估价对象实物状况 ", array('name' => '宋体', 'size' => 13.5, 'bold' => true, 'color' => '1B2232', 'align' => 'center'), 'w100');
        $section->addText("    2.1 标的区域状况", 'eightF', 'w100');
        $section->addText("    评估标的位于沛县沛县乡镇评税片区，本市运河路北侧住宅小区内，附近有江南新天地、御景花园、张庄新村等住宅小区，该区域水、电、电话、有线电视等基础设施配套齐全，附近有学校、超市、菜场，商业、服务网点齐全，附近有公交站台，生活较方便。", 'eightF', 'w100');

        $textrun = $section->addTextRun();
        $textrun->addImage('http://blog.aaayun.cc/logo.png', ["width" => 200, 'height' => 150]);
        $textrun->addImage('http://blog.aaayun.cc/logo.png', ["width" => 200, 'height' => 150, 'center' => 'right']);
        $textrun->addImage('http://blog.aaayun.cc/logo.png', ["width" => 200, 'height' => 150, 'center' => 'right']);

        $section->addText("    2.1.1区位价格状况", 'eightF', 'w100');
        $section->addText("    龙润华府小区网上无挂牌信息，附近张庄新村住宅2021年06月（估价时点）挂牌均价3412元/㎡，较去年年初上升3.6%。待评估的龙润华府附近张庄新村二手房近六个月挂牌价格波动区间为3100-3400元/㎡。", 'eightF', 'w100');
        $textrun = $section->addTextRun();
        $textrun->addImage('http://blog.aaayun.cc/logo.png', ["width" => 500, 'height' => 180]);
        $section->addText("    均价说明：此小区物业类型有普通多层、小高层，房源均价为普通多层、小高层包含毛坯价值以及附带装修价值。", 'eightF');
        $section->addText("    2.2 标的权益及实体状况 	", 'eightF');
        $section->addText("    该房屋为6层住宅楼的第1层，建于2016年，钢混结构，建筑面积为99.36㎡。用途：住宅。", 'eightF');
        $section->addText("    标的土地使用权类型：出让，土地用途：城镇住宅用地，分摊的土地使用权面积：12.0㎡。", 'eightF');
        $section->addText("五、价值时点", array('name' => '宋体', 'size' => 15, 'bold' => true, 'color' => '1B2232', 'align' => 'center'), 'w100');
        $section->addText("    根据《房地产估价规范》，房地产估价的价值时点原则上为完成估价对象实地查勘之日，故本次房地产估价师实地查勘之日2021年06月01日作为本次评估的价值时点。 ", 'eightF');
        $section->addText("六、价值类型", array('name' => '宋体', 'size' => 15, 'bold' => true, 'color' => '1B2232', 'align' => 'center'), 'w100');
        $section->addText("    本次估价采用公开市场价值标准，即本报告评估估价对象在目前情况下，于价值时点的市场价值，是未设定法定优先受偿权利下的市场价值。 ", 'eightF');
        $section->addText("七、估价原则", array('name' => '宋体', 'size' => 15, 'bold' => true, 'color' => '1B2232', 'align' => 'center'), 'w100');
        $section->addText("    本估价报告在遵循独立、客观、公正原则；合法原则；价值时点原则；替代原则；最高最佳利用原则；结合估价目的对估价对象进行估价。具体依据如下估价原则： ", 'eightF');
        $section->addText("    1.独立、客观、公正原则：独立、客观、公正原则要求评估价值应为对各方估价利害关系人均是公平合理的价值或价格。 ", 'eightF');
        $section->addText("    2.合法原则：合法原则要求评估价值应为在依法判定的估价对象状况下的价值或价格。 ", 'eightF');
        $section->addText("    3.价值时点原则：价值时点原则要求评估价值应为在根据估价目的确定的某一特定时间的价值或价格。", 'eightF');
        $section->addText("    4.替代原则：替代原则要求评估价值与估价对象的类似房地产在同等条件下的价值或价格偏差应在合理范围内。", 'eightF');
        $section->addText("    5.最高最佳利用原则：最高最佳利用原则要求评估价值应为在估计对象最高最佳利用状况下的价值或价格。", 'eightF');
        $section->addText("    6.预期收益原则：要求估价结果应充分考虑到预期潜在的收益因素，动态分析得出委托对象的价格。", 'eightF');
        $section->addText("八、估价依据", array('name' => '宋体', 'size' => 15, 'bold' => true, 'color' => '1B2232', 'align' => 'center'), 'w100');
        $section->addText("    1、法律、法规和政策性文件", 'eightF', 'w100');
        $section->addText("    1.1 《中华人民共和国民法典》；", 'eightF', 'w100');
        $section->addText("    1.2《中华人民共和国城市房地产管理法》；", 'eightF', 'w100');
        $section->addText("    1.3《中华人民共和国土地管理法》；", 'eightF', 'w100');
        $section->addText("    1.4《中华人民共和国城乡规划法》；", 'eightF', 'w100');
        $section->addText("    1.5《中华人民共和国城镇国有土地使用权出让和转让暂行条例》；", 'eightF', 'w100');
        $section->addText("    1.6《中华人民共和国房地产税暂行条例》；", 'eightF', 'w100');
        $section->addText("    2、技术标准、规范、规程", 'eightF', 'w100');
        $section->addText("    2.1《房地产估价规范》（GB/T 50291-2015）；", 'eightF', 'w100');
        $section->addText("    2.2《城镇土地估价规程》（GB/T 18508-2014）；", 'eightF', 'w100');
        $section->addText("    2.3《房地产估价基本术语标准》（GB/T 50899-2013）；", 'eightF', 'w100');
        $section->addText("    3、委托人提供的相关资料", 'eightF', 'w100');
        $section->addText("    3.1《江苏省存量房交易纳税评估系统业务规程》（2020年11月修订版）", 'eightF', 'w100');
        $section->addText("    3.2估价所需相关资料；", 'eightF', 'w100');
        $section->addText("    4、估价人员调查收集的相关资料", 'eightF', 'w100');
        $section->addText("    4.1 估价人员实地查勘和调查收集的有关估价对象权属、基础设施、宗地条件方面的资料；", 'eightF', 'w100');
        $section->addText("    4.2 估价对象所有区域的房地产市场状况、同类房地产市场交易等数据资料；", 'eightF', 'w100');
        $section->addText("    4.3估价人员实地拍摄的有关估价对象土地利用、建筑物状况、周边环境的照片；", 'eightF', 'w100');
        $section->addText("    4.4 估价人员实地查勘和调查收集的有关估价对象建筑物状况资料。", 'eightF', 'w100');
        $section->addText("九、估价方法", array('name' => '宋体', 'size' => 15, 'bold' => true, 'color' => '1B2232', 'align' => 'center'), 'w100');
        $section->addText("    估价人员在认真分析所掌握的资料并对邻近类似房地产进行实地勘查、调查后，根据估价对象的特点，遵照国家有关法律、法规、估价技术标准，经过研究，根据《房地产估价规范》4.2.3条款：同类房地产有较多交易的房地产，应选用比较法估价；收益性房地产，应选用收益法估价；假设可独立进行重新开发建设的房地产，应选用成本法估价；具有开发或再开发潜力且开发完成后的价值可采用比较法、收益法等成本法以外的方法评估的房地产，应选用假设开发法估价。本宗房地产因类似房地产买卖实例较多，且不适合采用成本法等其他方法，确定采用比较法进行评估。", 'eightF', 'w100');
        $section->addText("    比较法是将估价对象与估价时点近期有过交易的类似房地产进行比较，对这些类似房地产的已知价格作适当的修正，以此估算估价对象的客观合理价格或价值的方法。", 'eightF', 'w100');
//        $section->addText("    比准价格 V = Vs×P1×P2 ×P3×P4 - Va - Vb （元/平方米）", 'eightF', 'w100');
//        $section->addText("      Vs—可比实例的正常市场成交价格；", 'eightFG', 'w100');
//        $section->addText("      P1—交易情况修正，采用正常市场买卖交易实例为可比实例的不修正，此处数值为1；", 'eightFG', 'w100');
//        $section->addText("      P2—交易日期修正，将可比实例交易时的价格调整为估价价值时点（基准日）下的价格；", 'eightFG', 'w100');
//        $section->addText("          P2=基准日月度的区域价格指数（或市场挂牌均价） 可比实例交易月度的区域价格指数（或市场挂牌均价） ", 'eightFG', 'w100');
//        $section->addText("      P3—实体状况常规修正，具体系数选定参见表3-8（P9页）", 'eightFG', 'w100');
//        $section->addText("        P3＝标准房实物状况各修正系数的乘积 可比案例实物状况各修正系数的乘积 ", 'eightFG', 'w100');
//        $section->addText("      P4—区位状况修正，具体系数选定参见表3-8（P9页）；", 'eightFG', 'w100');
//        $section->addText("        P4＝标准房区位状况各因素评分之和 可比案例区位状况各因素评分之和 ", 'eightFG', 'w100');
//        $section->addText("      Va—单位面积装修重置价", 'eightFG', 'w100');
//        $section->addText("      Vb—单位面积附属物折算单价，附属物系指车库、阁楼、储藏室", 'eightFG', 'w100');
//        $section->addText("    ●单位面积装修重置价Va= 装修档次标准（P5）*装修成新率【1—装修已使用年限 装修的耐用年限 】", 'eightF', 'w100');
//        $section->addText("      P5—实体状况特殊修正系数A。", 'eightFG', 'w100');
//        $section->addText("      注：住宅的装修耐用年限一般设定为5-8年，计算时统一设定为8年。", 'eightFG', 'w100');
//        $section->addText("    ●单位面积附属物计算单价Vb=（Vs×P1×P2×P3×P4 - Va）×P6×S1÷S2", 'eightF', 'w100');
//        $section->addText("      P6—实体状况特殊修正系数B，包括车库、阁楼、储藏室计价系数，用来计算扣除附属物价格。", 'eightFG', 'w100');
//        $section->addText("      S1—附属物面积", 'eightFG', 'w100');
//        $section->addText("      S2—房产建筑面积", 'eightFG', 'w100');
        $textrun = $section->addTextRun();
        $textrun->addImage(PUB . 'image/s1.png', ["width" => 470, 'height' => 580]);

        $section->addText("十、估价结果", array('name' => '宋体', 'size' => 15, 'bold' => true, 'color' => '1B2232', 'align' => 'center'), 'w100');
        $section->addText("    估价人员根据估价目的，遵循估价原则，按照估价程序，选用适宜合理的估价方法，在认真分析现有资料的基础上，经过测算，结合估价经验，确定估价对象于价值时点的市场参考价为人民币单价：每平方米贰仟柒佰壹拾元整（RMB2710元/平方米）", 'eightF', 'w100');

        $styleTable = array('borderSize'=>6, 'borderColor'=>'000000', 'cellMargin'=>80);//表格样式
        $fontStyle = array('bold'=>false, 'align'=>'center', 'size' => 11);//文字样式

        $fancyTableStyleName = 'Fancy Table';
        $fancyTableStyle = array(
            'borderSize' => 1,
        );

        $fontStyle = array('bold'=>false, 'align'=>'center', 'size' => 11);//文字样式
        $cellColSpan = array('gridSpan' => 2, 'valign' => 'center'); // 单元格列合并
        $cellHCentered = array('alignment' => Jc::CENTER); // 水平居中

        $phpWord->addTableStyle($fancyTableStyleName, $fancyTableStyle, []);
        $table = $section->addTable($fancyTableStyleName);

        $table->addRow(200);
        $cell2 = $table->addCell(4400, $cellColSpan);
        $textrun2 = $cell2->addTextRun($cellHCentered);
        $textrun2->addText('评估方法/估价方法');

        $table->addCell(4400)->addText('比较法',$fontStyle);


        $row = $table->addRow();
        $row->addCell(2000, array('gridSpan' => 1, 'vMerge' => 'restart'))->addText('评估结果');
        $row->addCell(1000)->addText('总价（万元）');
        $table->addCell(2200)->addText('26.93',$fontStyle);


        $row = $table->addRow();
        $row->addCell(2000, array('vMerge' => 'continue', 'gridSpan' => 1));
        $row->addCell(1000)->addText('单价（元/m2）');
        $table->addCell(2200)->addText('2710',$fontStyle);

        $section->addText("    本估价结果是估价对象在价值时点未设立法定优先受偿权利下的市场价值。本次评估过程中未发现存在法定优先受偿权利。", 'eightF', 'w100');
        $section->addText("十一、注册房地产估价师", array('name' => '宋体', 'size' => 15, 'bold' => true, 'color' => '1B2232', 'align' => 'center'), 'w100');
        $table = $section->addTable('table_1');

        $table->addRow(200);
        $table->addCell(2200)->addText('姓名',$fontStyle);
        $table->addCell(2200)->addText('注册号',$fontStyle);
        $table->addCell(2200)->addText('签名',$fontStyle);
        $table->addCell(2200)->addText('签名日期',$fontStyle);

        $table->addRow(200);
        $table->addCell(2200)->addText('周  擎',$fontStyle);
        $table->addCell(2200)->addText('3220210187',$fontStyle);
        $table->addCell(2200)->addText('',$fontStyle);
        $table->addCell(2200)->addText('2021年06月06日',$fontStyle);

        $table->addRow(200);
        $table->addCell(2200)->addText('汪雪娟',$fontStyle);
        $table->addCell(2200)->addText('3220030108',$fontStyle);
        $table->addCell(2200)->addText('',$fontStyle);
        $table->addCell(2200)->addText('2021年06月06日',$fontStyle);
        $section->addText("十二、实地查勘期", array('name' => '宋体', 'size' => 15, 'bold' => true, 'color' => '1B2232', 'align' => 'center'), 'w100');
        $section->addText("    本次实地查勘于2021年06月01日。", 'eightF', 'w100');
        $section->addText("十三、估价作业期", array('name' => '宋体', 'size' => 15, 'bold' => true, 'color' => '1B2232', 'align' => 'center'), 'w100');
        $section->addText("    本次评估起于2021年06月01日，终于2021年06月06日。", 'eightF', 'w100');


    }


}