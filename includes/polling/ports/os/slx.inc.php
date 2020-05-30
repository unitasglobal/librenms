<?php

$slx_vlan_stats = snmpwalk_group($device, 'extremeVlanStatsGroup', 'EXTREME-BASE-MIB', 4);

$slx_vlan_ports = [];

foreach ($slx_vlan_stats as $index => $value) {
	preg_match( '/extremeVlanStatsGroup.2.1.(\d+).(\d+).(4\.[\.\d]+)/', $index, $matches);
	if(!empty($matches[3])) {
		$key = $matches[2].$matches[3];
		if ($matches[1] == '2') {
			$ifIndex = $matches[2].sprintf('%04d', $value);
			$slx_vlan_ports[$key] = [];
			$slx_vlan_ports[$key]['ifIndex'] = $ifIndex;
			if (array_key_exists (matches[2], $port_stats) ) {
				$slx_vlan_ports[$key]['ifName'] = $port_stats[$matches[2]]['ifName'].'.'.$value;
				$slx_vlan_ports[$key]['ifDescr'] = $port_stats[$matches[2]]['ifDescr'].'.'.$value;
				$slx_vlan_ports[$key]['ifAlias'] = $port_stats[$matches[2]]['ifAlias'].'  VLAN '.$value;
				$slx_vlan_ports[$key]['ifAdminStatus'] = $port_stats[$matches[2]]['ifAdminStatus'];
				$slx_vlan_ports[$key]['ifOperStatus'] = $port_stats[$matches[2]]['ifOperStatus'];
				$slx_vlan_ports[$key]['ifHighSpeed'] = $port_stats[$matches[2]]['ifHighSpeed'];
			}
			$slx_vlan_ports[$key]['ifType'] = 'l2vlan';
		} elseif ($matches[1] == '7') {
			$slx_vlan_ports[$key]['ifInOctets'] = $value;
		} elseif ($matches[1] == '8') {
			$slx_vlan_ports[$key]['ifInUcastPkts'] = $value;
		} elseif ($matches[1] == '12') {
			$slx_vlan_ports[$key]['ifOutOctets'] = $value;
		} elseif ($matches[1] == '13') {
			$slx_vlan_ports[$key]['ifOutUcastPkts'] = $value;
		}
	}
}

foreach ($slx_vlan_ports as $slx_port) {
        $ifIndex = $slx_port['ifIndex'];
	unset($slx_port['ifIndex']);
        $port_stats[$ifIndex] = $slx_port;
}

unset($slx_vlan_stats, $slx_vlan_ports);
