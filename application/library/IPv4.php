<?php

/**
 * 由base.class.php拆出
 * IPV4掩码计算工具类
 * @author junzhong
 *
 */
class ipv4
{
	var $address;
	var $netbits;
	//--------------
	// Create new class
	function ipv4($address,$netbits)
	{
		$this->address = $address;
		$this->netbits = $netbits;
	}
	//--------------
	// Return the IP address
	function address() { return ($this->address); }
	//--------------
	// Return the netbits
	function netbits() { return ($this->netbits); }
	//--------------
	// Return the netmask
	function netmask()
	{
		return (long2ip(bindec(decbin(ip2long("255.255.255.255")))
				<< (32-$this->netbits)));
	}
	//--------------
	// Return the network that the address sits in
	function network()
	{
		return (long2ip((bindec(decbin(ip2long($this->address))))
				& (ip2long($this->netmask()))));
	}
	//--------------
	// Return the broadcast that the address sits in
	function broadcast()
	{
		return (long2ip(bindec(decbin(ip2long($this->network())))
				| (~(bindec(decbin(ip2long($this->netmask())))))));
	}
	//--------------
	// Return the inverse mask of the netmask
	function inverse()
	{
		return (long2ip(~(bindec(decbin(ip2long("255.255.255.255")))
				<< (32-$this->netbits))));
	}

	function ip_range($ip){
		$network_long=bindec(decbin(ip2long($this->network())));
		$broadcast_long=bindec(decbin(ip2long($this->broadcast())));
			
		if(!is_long($ip)){
			$ip=bindec(decbin(ip2long($ip)));
		}
			
		return ($ip>$network_long)&&($ip<$broadcast_long);
	}

}