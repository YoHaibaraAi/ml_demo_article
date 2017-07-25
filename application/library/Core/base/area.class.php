<?php
/**
 * 地理类
 * @author junzhong
 *
 */
class area{
	
	public function __construct(){

	}
	
	/**
	 * 取得省列表
	 */
	public function getProvinces(){
		
		$config=$this->getConfig();
		
		$province_list=array();
		
		foreach($config['province'] as $key=>$data){
			$province_list[$key]=$data['name'];
		}
		
		return $province_list;
	}
	
	/**
	 * 根据省取得市列表
	 * @param $province_id	
	 */
	public function getCitys($province_id){

		$config=$this->getConfig();

		return $config['city']["{$province_id}"];
	}
	
	/**
	 * 生成json
	 * @return string
	 */
	public function makeJson(){
		
		$config=$this->getConfig();
		
		foreach($config['province'] as $province_id=>$data){
			
			$config['province']["{$province_id}"]["citys"]=$this->getCitys($province_id);
		}
		
		return json_encode($config['province']);
	}
	
	/**
	 * 生成数组
	 * @return string
	 */
	public function makeArray(){
		
		$config=$this->getConfig();
		
		foreach($config['province'] as $province_id=>$data){
				
			$config['province']["{$province_id}"]["citys"]=$this->getCitys($province_id);
		}
		
		return $config['province'];
	}
	
	/**
	 * 存储配置信息的方法
	 */
	private function getConfig(){
		
		$config=array();
		/**
		 * 省市配置
		 */
		$config['province']['34']['id']='34';											//省份id
		$config['province']['34']['name']='安徽';										//省份名称
		$config['city']['34']['1']='合肥';												//该省的某个id的城市的名称
		$config['city']['34']['2']='芜湖';
		$config['city']['34']['3']='蚌埠';
		$config['city']['34']['4']='淮南';
		$config['city']['34']['5']='马鞍山';
		$config['city']['34']['6']='淮北';
		$config['city']['34']['7']='铜陵';
		$config['city']['34']['8']='安庆';
		$config['city']['34']['10']='黄山';
		$config['city']['34']['11']='滁州';
		$config['city']['34']['12']='阜阳';
		$config['city']['34']['13']='宿州';
		$config['city']['34']['14']='巢湖';
		$config['city']['34']['15']='六安';
		$config['city']['34']['16']='亳州';
		$config['city']['34']['17']='池州';
		$config['city']['34']['18']='宣城';
		$config['province']['34']['cid']='1,2,3,4,5,6,7,8,10,11,12,13,14,15,16,17,18';	//该省的所有城市的id
		
		$config['province']['11']['id']='11';
		$config['province']['11']['name']='北京';
		$config['city']['11']['1']='东城区';
		$config['city']['11']['2']='西城区';
		$config['city']['11']['3']='崇文区';
		$config['city']['11']['4']='宣武区';
		$config['city']['11']['5']='朝阳区';
		$config['city']['11']['6']='丰台区';
		$config['city']['11']['7']='石景山区';
		$config['city']['11']['8']='海淀区';
		$config['city']['11']['9']='门头沟区';
		$config['city']['11']['11']='房山区';
		$config['city']['11']['12']='通州区';
		$config['city']['11']['13']='顺义区';
		$config['city']['11']['14']='昌平区';
		$config['city']['11']['15']='大兴区';
		$config['city']['11']['16']='怀柔区';
		$config['city']['11']['17']='平谷区';
		$config['city']['11']['28']='密云县';
		$config['city']['11']['29']='延庆县';
		$config['province']['11']['cid']='1,2,3,4,5,6,7,8,9,11,12,13,14,15,16,17,28,29';
		
		$config['province']['50']['id']='50';
		$config['province']['50']['name']='重庆';
		$config['city']['50']['1']='万州区';
		$config['city']['50']['2']='涪陵区';
		$config['city']['50']['3']='渝中区';
		$config['city']['50']['4']='大渡口区';
		$config['city']['50']['5']='江北区';
		$config['city']['50']['6']='沙坪坝区';
		$config['city']['50']['7']='九龙坡区';
		$config['city']['50']['8']='南岸区';
		$config['city']['50']['9']='北碚区';
		$config['city']['50']['10']='万盛区';
		$config['city']['50']['11']='双桥区';
		$config['city']['50']['12']='渝北区';
		$config['city']['50']['13']='巴南区';
		$config['city']['50']['14']='黔江区';
		$config['city']['50']['15']='长寿区';
		$config['city']['50']['22']='綦江县';
		$config['city']['50']['23']='潼南县';
		$config['city']['50']['24']='铜梁县';
		$config['city']['50']['25']='大足县';
		$config['city']['50']['26']='荣昌县';
		$config['city']['50']['27']='璧山县';
		$config['city']['50']['28']='梁平县';
		$config['city']['50']['29']='城口县';
		$config['city']['50']['30']='丰都县';
		$config['city']['50']['31']='垫江县';
		$config['city']['50']['32']='武隆县';
		$config['city']['50']['33']='忠县';
		$config['city']['50']['34']='开县';
		$config['city']['50']['35']='云阳县';
		$config['city']['50']['36']='奉节县';
		$config['city']['50']['37']='巫山县';
		$config['city']['50']['38']='巫溪县';
		$config['city']['50']['40']='石柱土家族自治县';
		$config['city']['50']['41']='秀山土家族苗族自治县';
		$config['city']['50']['42']='酉阳土家族苗族自治县';
		$config['city']['50']['43']='彭水苗族土家族自治县';
		$config['city']['50']['81']='江津市';
		$config['city']['50']['82']='合川市';
		$config['city']['50']['83']='永川市';
		$config['city']['50']['84']='南川市';
		$config['province']['50']['cid']='1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,40,41,42,43,81,82,83,84';
		
		$config['province']['35']['id']='35';
		$config['province']['35']['name']='福建';
		$config['city']['35']['1']='福州';
		$config['city']['35']['2']='厦门';
		$config['city']['35']['3']='莆田';
		$config['city']['35']['4']='三明';
		$config['city']['35']['5']='泉州';
		$config['city']['35']['6']='漳州';
		$config['city']['35']['7']='南平';
		$config['city']['35']['8']='龙岩';
		$config['city']['35']['9']='宁德';
		$config['province']['35']['cid']='1,2,3,4,5,6,7,8,9';
		
		$config['province']['62']['id']='62';
		$config['province']['62']['name']='甘肃';
		$config['city']['62']['1']='兰州';
		$config['city']['62']['2']='嘉峪关';
		$config['city']['62']['3']='金昌';
		$config['city']['62']['4']='白银';
		$config['city']['62']['5']='天水';
		$config['city']['62']['6']='武威';
		$config['city']['62']['7']='张掖';
		$config['city']['62']['8']='平凉';
		$config['city']['62']['9']='酒泉';
		$config['city']['62']['10']='庆阳';
		$config['city']['62']['24']='定西';
		$config['city']['62']['26']='陇南';
		$config['city']['62']['29']='临夏';
		$config['city']['62']['30']='甘南';
		$config['province']['62']['cid']='1,2,3,4,5,6,7,8,9,10,24,26,29,30';
		
		$config['province']['44']['id']='44';
		$config['province']['44']['name']='广东';
		$config['city']['44']['1']='广州';
		$config['city']['44']['2']='韶关';
		$config['city']['44']['3']='深圳';
		$config['city']['44']['4']='珠海';
		$config['city']['44']['5']='汕头';
		$config['city']['44']['6']='佛山';
		$config['city']['44']['7']='江门';
		$config['city']['44']['8']='湛江';
		$config['city']['44']['9']='茂名';
		$config['city']['44']['12']='肇庆';
		$config['city']['44']['13']='惠州';
		$config['city']['44']['14']='梅州';
		$config['city']['44']['15']='汕尾';
		$config['city']['44']['16']='河源';
		$config['city']['44']['17']='阳江';
		$config['city']['44']['18']='清远';
		$config['city']['44']['19']='东莞';
		$config['city']['44']['20']='中山';
		$config['city']['44']['51']='潮州';
		$config['city']['44']['52']='揭阳';
		$config['city']['44']['53']='云浮';
		$config['province']['44']['cid']='1,2,3,4,5,6,7,8,9,12,13,14,15,16,17,18,19,20,51,52,53';
		
		$config['province']['45']['id']='45';
		$config['province']['45']['name']='广西';
		$config['city']['45']['1']='南宁';
		$config['city']['45']['2']='柳州';
		$config['city']['45']['3']='桂林';
		$config['city']['45']['4']='梧州';
		$config['city']['45']['5']='北海';
		$config['city']['45']['6']='防城港';
		$config['city']['45']['7']='钦州';
		$config['city']['45']['8']='贵港';
		$config['city']['45']['9']='玉林';
		$config['city']['45']['10']='百色';
		$config['city']['45']['11']='贺州';
		$config['city']['45']['12']='河池';
		$config['city']['45']['21']='南宁';
		$config['city']['45']['22']='柳州';
		$config['province']['45']['cid']='1,2,3,4,5,6,7,8,9,10,11,12,21,22';
		
		$config['province']['52']['id']='52';
		$config['province']['52']['name']='贵州';
		$config['city']['52']['1']='贵阳';
		$config['city']['52']['2']='六盘水';
		$config['city']['52']['3']='遵义';
		$config['city']['52']['4']='安顺';
		$config['city']['52']['22']='铜仁';
		$config['city']['52']['23']='黔西南';
		$config['city']['52']['24']='毕节';
		$config['city']['52']['26']='黔东南';
		$config['city']['52']['27']='黔南';
		$config['province']['52']['cid']='1,2,3,4,22,23,24,26,27';
		
		$config['province']['46']['id']='46';
		$config['province']['46']['name']='海南';
		$config['city']['46']['1']='海口';
		$config['city']['46']['2']='三亚';
		$config['city']['46']['90']='其他';
		$config['province']['46']['cid']='1,2,90';
		
		$config['province']['13']['id']='13';
		$config['province']['13']['name']='河北';
		$config['city']['13']['1']='石家庄';
		$config['city']['13']['2']='唐山';
		$config['city']['13']['3']='秦皇岛';
		$config['city']['13']['4']='邯郸';
		$config['city']['13']['5']='邢台';
		$config['city']['13']['6']='保定';
		$config['city']['13']['7']='张家口';
		$config['city']['13']['8']='承德';
		$config['city']['13']['9']='沧州';
		$config['city']['13']['10']='廊坊';
		$config['city']['13']['11']='衡水';
		$config['province']['13']['cid']='1,2,3,4,5,6,7,8,9,10,11';
		
		$config['province']['23']['id']='23';
		$config['province']['23']['name']='黑龙江';
		$config['city']['23']['1']='哈尔滨';
		$config['city']['23']['2']='齐齐哈尔';
		$config['city']['23']['3']='鸡西';
		$config['city']['23']['4']='鹤岗';
		$config['city']['23']['5']='双鸭山';
		$config['city']['23']['6']='大庆';
		$config['city']['23']['7']='伊春';
		$config['city']['23']['8']='佳木斯';
		$config['city']['23']['9']='七台河';
		$config['city']['23']['10']='牡丹江';
		$config['city']['23']['11']='黑河';
		$config['city']['23']['12']='绥化';
		$config['city']['23']['27']='大兴安岭';
		$config['province']['23']['cid']='1,2,3,4,5,6,7,8,9,10,11,12,27';
		
		$config['province']['41']['id']='41';
		$config['province']['41']['name']='河南';
		$config['city']['41']['1']='郑州';
		$config['city']['41']['2']='开封';
		$config['city']['41']['3']='洛阳';
		$config['city']['41']['4']='平顶山';
		$config['city']['41']['5']='安阳';
		$config['city']['41']['6']='鹤壁';
		$config['city']['41']['7']='新乡';
		$config['city']['41']['8']='焦作';
		$config['city']['41']['9']='濮阳';
		$config['city']['41']['10']='许昌';
		$config['city']['41']['11']='漯河';
		$config['city']['41']['12']='三门峡';
		$config['city']['41']['13']='南阳';
		$config['city']['41']['14']='商丘';
		$config['city']['41']['15']='信阳';
		$config['city']['41']['16']='周口';
		$config['city']['41']['17']='驻马店';
		$config['province']['41']['cid']='1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17';
		
		$config['province']['42']['id']='42';
		$config['province']['42']['name']='湖北';
		$config['city']['42']['1']='武汉';
		$config['city']['42']['2']='黄石';
		$config['city']['42']['3']='十堰';
		$config['city']['42']['5']='宜昌';
		$config['city']['42']['6']='襄樊';
		$config['city']['42']['7']='鄂州';
		$config['city']['42']['8']='荆门';
		$config['city']['42']['9']='孝感';
		$config['city']['42']['10']='荆州';
		$config['city']['42']['11']='黄冈';
		$config['city']['42']['12']='咸宁';
		$config['city']['42']['13']='随州';
		$config['city']['42']['28']='恩施土家族苗族自治州';
		$config['province']['42']['cid']='1,2,3,5,6,7,8,9,10,11,12,13,28';
		
		$config['province']['43']['id']='43';
		$config['province']['43']['name']='湖南';
		$config['city']['43']['1']='长沙';
		$config['city']['43']['2']='株洲';
		$config['city']['43']['3']='湘潭';
		$config['city']['43']['4']='衡阳';
		$config['city']['43']['5']='邵阳';
		$config['city']['43']['6']='岳阳';
		$config['city']['43']['7']='常德';
		$config['city']['43']['8']='张家界';
		$config['city']['43']['9']='益阳';
		$config['city']['43']['10']='郴州';
		$config['city']['43']['11']='永州';
		$config['city']['43']['12']='怀化';
		$config['city']['43']['13']='娄底';
		$config['city']['43']['31']='湘西土家族苗族自治州';
		$config['province']['43']['cid']='1,2,3,4,5,6,7,8,9,10,11,12,13,31';
		
		$config['province']['15']['id']='15';
		$config['province']['15']['name']='内蒙古';
		$config['city']['15']['1']='呼和浩特';
		$config['city']['15']['2']='包头';
		$config['city']['15']['3']='乌海';
		$config['city']['15']['4']='赤峰';
		$config['city']['15']['5']='通辽';
		$config['city']['15']['6']='鄂尔多斯';
		$config['city']['15']['7']='呼伦贝尔';
		$config['city']['15']['22']='兴安盟';
		$config['city']['15']['25']='锡林郭勒盟';
		$config['city']['15']['26']='乌兰察布盟';
		$config['city']['15']['28']='巴彦淖尔盟';
		$config['city']['15']['29']='阿拉善盟';
		$config['province']['15']['cid']='1,2,3,4,5,6,7,22,25,26,28,29';
		
		$config['province']['32']['id']='32';
		$config['province']['32']['name']='江苏';
		$config['city']['32']['1']='南京';
		$config['city']['32']['2']='无锡';
		$config['city']['32']['3']='徐州';
		$config['city']['32']['4']='常州';
		$config['city']['32']['5']='苏州';
		$config['city']['32']['6']='南通';
		$config['city']['32']['7']='连云港';
		$config['city']['32']['8']='淮安';
		$config['city']['32']['9']='盐城';
		$config['city']['32']['10']='扬州';
		$config['city']['32']['11']='镇江';
		$config['city']['32']['12']='泰州';
		$config['city']['32']['13']='宿迁';
		$config['province']['32']['cid']='1,2,3,4,5,6,7,8,9,10,11,12,13';
		
		$config['province']['36']['id']='36';
		$config['province']['36']['name']='江西';
		$config['city']['36']['1']='南昌';
		$config['city']['36']['2']='景德镇';
		$config['city']['36']['3']='萍乡';
		$config['city']['36']['4']='九江';
		$config['city']['36']['5']='新余';
		$config['city']['36']['6']='鹰潭';
		$config['city']['36']['7']='赣州';
		$config['city']['36']['8']='吉安';
		$config['city']['36']['9']='宜春';
		$config['city']['36']['10']='抚州';
		$config['city']['36']['11']='上饶';
		$config['province']['36']['cid']='1,2,3,4,5,6,7,8,9,10,11';
		
		$config['province']['22']['id']='22';
		$config['province']['22']['name']='吉林';
		$config['city']['22']['1']='长春';
		$config['city']['22']['2']='吉林';
		$config['city']['22']['3']='四平';
		$config['city']['22']['4']='辽源';
		$config['city']['22']['5']='通化';
		$config['city']['22']['6']='白山';
		$config['city']['22']['7']='松原';
		$config['city']['22']['8']='白城';
		$config['city']['22']['24']='延边朝鲜族自治州';
		$config['province']['22']['cid']='1,2,3,4,5,6,7,8,24';
		
		$config['province']['21']['id']='21';
		$config['province']['21']['name']='辽宁';
		$config['city']['21']['1']='沈阳';
		$config['city']['21']['2']='大连';
		$config['city']['21']['3']='鞍山';
		$config['city']['21']['4']='抚顺';
		$config['city']['21']['5']='本溪';
		$config['city']['21']['6']='丹东';
		$config['city']['21']['7']='锦州';
		$config['city']['21']['8']='营口';
		$config['city']['21']['9']='阜新';
		$config['city']['21']['10']='辽阳';
		$config['city']['21']['11']='盘锦';
		$config['city']['21']['12']='铁岭';
		$config['city']['21']['13']='朝阳';
		$config['city']['21']['14']='葫芦岛';
		$config['province']['21']['cid']='1,2,3,4,5,6,7,8,9,10,11,12,13,14';
		
		$config['province']['64']['id']='64';
		$config['province']['64']['name']='宁夏';
		$config['city']['64']['1']='银川';
		$config['city']['64']['2']='石嘴山';
		$config['city']['64']['3']='吴忠';
		$config['city']['64']['4']='固原';
		$config['province']['64']['cid']='1,2,3,4';
		
		$config['province']['63']['id']='63';
		$config['province']['63']['name']='青海';
		$config['city']['63']['1']='西宁';
		$config['city']['63']['21']='海东';
		$config['city']['63']['22']='海北';
		$config['city']['63']['23']='黄南';
		$config['city']['63']['25']='海南';
		$config['city']['63']['26']='果洛';
		$config['city']['63']['27']='玉树';
		$config['city']['63']['28']='海西';
		$config['province']['63']['cid']='1,21,22,23,25,26,27,28';
		
		$config['province']['14']['id']='14';
		$config['province']['14']['name']='山西';
		$config['city']['14']['1']='太原';
		$config['city']['14']['2']='大同';
		$config['city']['14']['3']='阳泉';
		$config['city']['14']['4']='长治';
		$config['city']['14']['5']='晋城';
		$config['city']['14']['6']='朔州';
		$config['city']['14']['7']='晋中';
		$config['city']['14']['8']='运城';
		$config['city']['14']['9']='忻州';
		$config['city']['14']['10']='临汾';
		$config['city']['14']['23']='吕梁';
		$config['province']['14']['cid']='1,2,3,4,5,6,7,8,9,10,23';
		
		$config['province']['37']['id']='37';
		$config['province']['37']['name']='山东';
		$config['city']['37']['1']='济南';
		$config['city']['37']['2']='青岛';
		$config['city']['37']['3']='淄博';
		$config['city']['37']['4']='枣庄';
		$config['city']['37']['5']='东营';
		$config['city']['37']['6']='烟台';
		$config['city']['37']['7']='潍坊';
		$config['city']['37']['8']='济宁';
		$config['city']['37']['9']='泰安';
		$config['city']['37']['10']='威海';
		$config['city']['37']['11']='日照';
		$config['city']['37']['12']='莱芜';
		$config['city']['37']['13']='临沂';
		$config['city']['37']['14']='德州';
		$config['city']['37']['15']='聊城';
		$config['city']['37']['16']='滨州';
		$config['city']['37']['17']='荷泽';
		$config['province']['37']['cid']='1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17';
		
		$config['province']['31']['id']='31';
		$config['province']['31']['name']='上海';
		$config['city']['31']['1']='黄浦区';
		$config['city']['31']['3']='卢湾区';
		$config['city']['31']['4']='徐汇区';
		$config['city']['31']['5']='长宁区';
		$config['city']['31']['6']='静安区';
		$config['city']['31']['7']='普陀区';
		$config['city']['31']['8']='闸北区';
		$config['city']['31']['9']='虹口区';
		$config['city']['31']['10']='杨浦区';
		$config['city']['31']['12']='闵行区';
		$config['city']['31']['13']='宝山区';
		$config['city']['31']['14']='嘉定区';
		$config['city']['31']['15']='浦东新区';
		$config['city']['31']['16']='金山区';
		$config['city']['31']['17']='松江区';
		$config['city']['31']['18']='青浦区';
		$config['city']['31']['19']='南汇区';
		$config['city']['31']['20']='奉贤区';
		$config['city']['31']['30']='崇明县';
		$config['province']['31']['cid']='1,3,4,5,6,7,8,9,10,12,13,14,15,16,17,18,19,20,30';
		
		$config['province']['51']['id']='51';
		$config['province']['51']['name']='四川';
		$config['city']['51']['1']='成都';
		$config['city']['51']['3']='自贡';
		$config['city']['51']['4']='攀枝花';
		$config['city']['51']['5']='泸州';
		$config['city']['51']['6']='德阳';
		$config['city']['51']['7']='绵阳';
		$config['city']['51']['8']='广元';
		$config['city']['51']['9']='遂宁';
		$config['city']['51']['10']='内江';
		$config['city']['51']['11']='乐山';
		$config['city']['51']['13']='南充';
		$config['city']['51']['14']='眉山';
		$config['city']['51']['15']='宜宾';
		$config['city']['51']['16']='广安';
		$config['city']['51']['17']='达州';
		$config['city']['51']['18']='雅安';
		$config['city']['51']['19']='巴中';
		$config['city']['51']['20']='资阳';
		$config['city']['51']['32']='阿坝';
		$config['city']['51']['33']='甘孜';
		$config['city']['51']['34']='凉山';
		$config['province']['51']['cid']='1,3,4,5,6,7,8,9,10,11,13,14,15,16,17,18,19,20,32,33,34';
		
		$config['province']['12']['id']='12';
		$config['province']['12']['name']='天津';
		$config['city']['12']['1']='和平区';
		$config['city']['12']['2']='河东区';
		$config['city']['12']['3']='河西区';
		$config['city']['12']['4']='南开区';
		$config['city']['12']['5']='河北区';
		$config['city']['12']['6']='红桥区';
		$config['city']['12']['7']='塘沽区';
		$config['city']['12']['8']='汉沽区';
		$config['city']['12']['9']='大港区';
		$config['city']['12']['10']='东丽区';
		$config['city']['12']['11']='西青区';
		$config['city']['12']['12']='津南区';
		$config['city']['12']['13']='北辰区';
		$config['city']['12']['14']='武清区';
		$config['city']['12']['15']='宝坻区';
		$config['city']['12']['21']='宁河县';
		$config['city']['12']['23']='静海县';
		$config['city']['12']['25']='蓟县';
		$config['province']['12']['cid']='1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,21,23,25';
		
		$config['province']['54']['id']='54';
		$config['province']['54']['name']='西藏';
		$config['city']['54']['1']='拉萨';
		$config['city']['54']['21']='昌都';
		$config['city']['54']['22']='山南';
		$config['city']['54']['23']='日喀则';
		$config['city']['54']['24']='那曲';
		$config['city']['54']['25']='阿里';
		$config['city']['54']['26']='林芝';
		$config['province']['54']['cid']='1,21,22,23,24,25,26';
		
		$config['province']['65']['id']='65';
		$config['province']['65']['name']='新疆';
		$config['city']['65']['1']='乌鲁木齐';
		$config['city']['65']['2']='克拉玛依';
		$config['city']['65']['21']='吐鲁番';
		$config['city']['65']['22']='哈密';
		$config['city']['65']['23']='昌吉';
		$config['city']['65']['27']='博尔塔拉';
		$config['city']['65']['28']='巴音郭楞';
		$config['city']['65']['29']='阿克苏';
		$config['city']['65']['30']='克孜勒苏';
		$config['city']['65']['31']='喀什';
		$config['city']['65']['32']='和田';
		$config['city']['65']['40']='伊犁';
		$config['city']['65']['42']='塔城';
		$config['city']['65']['43']='阿勒泰';
		$config['province']['65']['cid']='1,2,21,22,23,27,28,29,30,31,32,40,42,43';
		
		$config['province']['53']['id']='53';
		$config['province']['53']['name']='云南';
		$config['city']['53']['1']='昆明';
		$config['city']['53']['3']='曲靖';
		$config['city']['53']['4']='玉溪';
		$config['city']['53']['5']='保山';
		$config['city']['53']['6']='昭通';
		$config['city']['53']['23']='楚雄';
		$config['city']['53']['25']='红河';
		$config['city']['53']['26']='文山';
		$config['city']['53']['27']='思茅';
		$config['city']['53']['28']='西双版纳';
		$config['city']['53']['29']='大理';
		$config['city']['53']['31']='德宏';
		$config['city']['53']['32']='丽江';
		$config['city']['53']['33']='怒江';
		$config['city']['53']['34']='迪庆';
		$config['city']['53']['35']='临沧';
		$config['province']['53']['cid']='1,3,4,5,6,23,25,26,27,28,29,31,32,33,34,35';
		
		$config['province']['33']['id']='33';
		$config['province']['33']['name']='浙江';
		$config['city']['33']['1']='杭州';
		$config['city']['33']['2']='宁波';
		$config['city']['33']['3']='温州';
		$config['city']['33']['4']='嘉兴';
		$config['city']['33']['5']='湖州';
		$config['city']['33']['6']='绍兴';
		$config['city']['33']['7']='金华';
		$config['city']['33']['8']='衢州';
		$config['city']['33']['9']='舟山';
		$config['city']['33']['10']='台州';
		$config['city']['33']['11']='丽水';
		$config['province']['33']['cid']='1,2,3,4,5,6,7,8,9,10,11';
		
		$config['province']['61']['id']='61';
		$config['province']['61']['name']='陕西';
		$config['city']['61']['1']='西安';
		$config['city']['61']['2']='铜川';
		$config['city']['61']['3']='宝鸡';
		$config['city']['61']['4']='咸阳';
		$config['city']['61']['5']='渭南';
		$config['city']['61']['6']='延安';
		$config['city']['61']['7']='汉中';
		$config['city']['61']['8']='榆林';
		$config['city']['61']['9']='安康';
		$config['city']['61']['10']='商洛';
		$config['province']['61']['cid']='1,2,3,4,5,6,7,8,9,10';
		
		$config['province']['71']['id']='71';
		$config['province']['71']['name']='台湾';
		$config['city']['71']['1']='台北';
		$config['city']['71']['2']='高雄';
		$config['city']['71']['90']='其他';
		$config['province']['71']['cid']='1,2,90';
		
		$config['province']['81']['id']='81';
		$config['province']['81']['name']='香港';
		$config['city']['81']['1']='香港';
		$config['province']['81']['cid']='1';
		
		$config['province']['82']['id']='82';
		$config['province']['82']['name']='澳门';
		$config['city']['82']['1']='澳门';
		$config['province']['82']['cid']='1';
		
		$config['province']['999']['id']='999';
		$config['province']['999']['name']='其他';
		$config['city']['999']['1']='其他';
		$config['province']['999']['cid']='1';
		
		return $config;
	}
	
	
	
}