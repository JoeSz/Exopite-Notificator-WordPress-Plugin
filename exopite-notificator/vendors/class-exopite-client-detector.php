<?php

if ( ! class_exists ( 'Exopite_Client_Detector' ) ) :
class Exopite_Client_Detector {

    /**
     * Exopite_Client_Detector::get_client();
     *
     * Exopite_Client_Detector::get_user_agent()
     * Exopite_Client_Detector::get_os( Exopite_Client_Detector::get_user_agent() );
     * Exopite_Client_Detector::get_platform()
     * Exopite_Client_Detector::get_ip();
     * Exopite_Client_Detector::get_browser();
     *
     * Exopite_Client_Detector::is_bot();
     * Exopite_Client_Detector::is_windows();
     * Exopite_Client_Detector::is_mac();
     * Exopite_Client_Detector::is_ipad();
     * Exopite_Client_Detector::is_iphone();
     * Exopite_Client_Detector::is_ios();
     * Exopite_Client_Detector::is_blackberry();
     * Exopite_Client_Detector::is_mobile_or_phone();
     * Exopite_Client_Detector::is_windows_mobile();
     * Exopite_Client_Detector::is_windows_desktop();
     * Exopite_Client_Detector::is_windows_tablet();
     * Exopite_Client_Detector::is_android();
     * Exopite_Client_Detector::is_linux_desktop();
     * Exopite_Client_Detector::is_android_mobile();
     * Exopite_Client_Detector::is_android_tablet();
     * Exopite_Client_Detector::is_mobile();
     * Exopite_Client_Detector::is_tablet();
     * Exopite_Client_Detector::is_desktop();
     */

    public static function get_client() {

        $user_agent             = self::get_user_agent();

        $client                 = self::get_browser();
        $client['ip']           = self::get_ip();
        $client['platform']     = self::get_platform();
        $client['os']           = self::get_os( $user_agent );
        $client['user_agent']   = $user_agent;

        return $client;

    }

    /**
     * return Operating System
     */
    public static function get_os( $agent ) {

        $ros[] = array('Windows XP', 'Windows XP');
        $ros[] = array('Windows NT 5.1|Windows NT5.1', 'Windows XP');
        $ros[] = array('Windows 2000', 'Windows 2000');
        $ros[] = array('Windows NT 5.0', 'Windows 2000');
        $ros[] = array('Windows NT 4.0|WinNT4.0', 'Windows NT');
        $ros[] = array('Windows NT 5.2', 'Windows Server 2003');
        $ros[] = array('Windows NT 6.0', 'Windows Vista');
        $ros[] = array('Windows NT 6.1', 'Windows 7');
        $ros[] = array('Windows NT 7.0', 'Windows 7');
        $ros[] = array('Windows NT 6.2', 'Windows 8');
        $ros[] = array('Windows NT 6.3', 'Windows 8.1');
        $ros[] = array('Windows NT 10.0', 'Windows 10');
        $ros[] = array('Windows CE', 'Windows CE');
        $ros[] = array('(media center pc).([0-9]{1,2}\.[0-9]{1,2})', 'Windows Media Center');
        $ros[] = array('(win)([0-9]{1,2}\.[0-9x]{1,2})', 'Windows');
        $ros[] = array('(win)([0-9]{2})', 'Windows');
        $ros[] = array('(windows)([0-9x]{2})', 'Windows');
        // Doesn't seem like these are necessary...not totally sure though..
        //$ros[] = array('(winnt)([0-9]{1,2}\.[0-9]{1,2}){0,1}', 'Windows NT');
        //$ros[] = array('(windows nt)(([0-9]{1,2}\.[0-9]{1,2}){0,1})', 'Windows NT'); // fix by bg
        $ros[] = array('Windows ME', 'Windows ME');
        $ros[] = array('Win 9x 4.90', 'Windows ME');
        $ros[] = array('Windows 98|Win98', 'Windows 98');
        $ros[] = array('Windows 95|win95', 'Windows 95');
        $ros[] = array('(windows)([0-9]{1,2}\.[0-9]{1,2})', 'Windows');
        $ros[] = array('win32', 'Windows');
        $ros[] = array('(java)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2})', 'Java');
        $ros[] = array('(Solaris)([0-9]{1,2}\.[0-9x]{1,2}){0,1}', 'Solaris');
        $ros[] = array('dos x86', 'DOS');
        $ros[] = array('unix', 'Unix');
        $ros[] = array('macintosh|Mac OS X', 'Mac OS X');
        $ros[] = array('Mac_PowerPC', 'Macintosh PowerPC');
        $ros[] = array('(mac|Macintosh)', 'Mac OS');
        $ros[] = array('(sunos)([0-9]{1,2}\.[0-9]{1,2}){0,1}', 'SunOS');
        $ros[] = array('(beos)([0-9]{1,2}\.[0-9]{1,2}){0,1}', 'BeOS');
        $ros[] = array('(risc os)([0-9]{1,2}\.[0-9]{1,2})', 'RISC OS');
        $ros[] = array('os/2', 'OS/2');
        $ros[] = array('freebsd', 'FreeBSD');
        $ros[] = array('openbsd', 'OpenBSD');
        $ros[] = array('netbsd', 'NetBSD');
        $ros[] = array('irix', 'IRIX');
        $ros[] = array('plan9', 'Plan9');
        $ros[] = array('osf', 'OSF');
        $ros[] = array('aix', 'AIX');
        $ros[] = array('GNU Hurd', 'GNU Hurd');
        $ros[] = array('(fedora)', 'Linux - Fedora');
        $ros[] = array('(kubuntu)', 'Linux - Kubuntu');
        $ros[] = array('(ubuntu)', 'Linux - Ubuntu');
        $ros[] = array('(debian)', 'Linux - Debian');
        $ros[] = array('(CentOS)', 'Linux - CentOS');
        $ros[] = array('(Mandriva).([0-9]{1,3}(\.[0-9]{1,3})?(\.[0-9]{1,3})?)', 'Linux - Mandriva');
        $ros[] = array('(SUSE).([0-9]{1,3}(\.[0-9]{1,3})?(\.[0-9]{1,3})?)', 'Linux - SUSE');
        $ros[] = array('(Dropline)', 'Linux - Slackware (Dropline GNOME)');
        $ros[] = array('(ASPLinux)', 'Linux - ASPLinux');
        $ros[] = array('(Red Hat)', 'Linux - Red Hat');
        // Loads of Linux machines will be detected as unix.
        // Actually, all of the linux machines I've checked have the 'X11' in the User Agent.
        //$ros[] = array('X11', 'Unix');
        $ros[] = array('(linux)', 'Linux');
        $ros[] = array('(amigaos)([0-9]{1,2}\.[0-9]{1,2})', 'AmigaOS');
        $ros[] = array('amiga-aweb', 'AmigaOS');
        $ros[] = array('amiga', 'Amiga');
        $ros[] = array('AvantGo', 'PalmOS');
        //$ros[] = array('(Linux)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3}(rel\.[0-9]{1,2}){0,1}-([0-9]{1,2}) i([0-9]{1})86){1}', 'Linux');
        //$ros[] = array('(Linux)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3}(rel\.[0-9]{1,2}){0,1} i([0-9]{1}86)){1}', 'Linux');
        //$ros[] = array('(Linux)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3}(rel\.[0-9]{1,2}){0,1})', 'Linux');
        $ros[] = array('[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3})', 'Linux');
        $ros[] = array('(webtv)/([0-9]{1,2}\.[0-9]{1,2})', 'WebTV');
        $ros[] = array('Dreamcast', 'Dreamcast OS');
        $ros[] = array('GetRight', 'Windows');
        $ros[] = array('go!zilla', 'Windows');
        $ros[] = array('gozilla', 'Windows');
        $ros[] = array('gulliver', 'Windows');
        $ros[] = array('ia archiver', 'Windows');
        $ros[] = array('NetPositive', 'Windows');
        $ros[] = array('mass downloader', 'Windows');
        $ros[] = array('microsoft', 'Windows');
        $ros[] = array('offline explorer', 'Windows');
        $ros[] = array('teleport', 'Windows');
        $ros[] = array('web downloader', 'Windows');
        $ros[] = array('webcapture', 'Windows');
        $ros[] = array('webcollage', 'Windows');
        $ros[] = array('webcopier', 'Windows');
        $ros[] = array('webstripper', 'Windows');
        $ros[] = array('webzip', 'Windows');
        $ros[] = array('wget', 'Windows');
        $ros[] = array('Java', 'Unknown');
        $ros[] = array('flashget', 'Windows');
        // delete next line if the script show not the right OS
        //$ros[] = array('(PHP)/([0-9]{1,2}.[0-9]{1,2})', 'PHP');
        $ros[] = array('MS FrontPage', 'Windows');
        $ros[] = array('(msproxy)/([0-9]{1,2}.[0-9]{1,2})', 'Windows');
        $ros[] = array('(msie)([0-9]{1,2}.[0-9]{1,2})', 'Windows');
        $ros[] = array('libwww-perl', 'Unix');
        $ros[] = array('UP.Browser', 'Windows CE');
        $ros[] = array('NetAnts', 'Windows');

        $file = count ( $ros );
        $os = '';
        for ( $n=0 ; $n<$file ; $n++ ){

            if ( preg_match('/'.$ros[$n][0].'/i' , $agent, $name)){
                // $os = @$ros[$n][1].' '.$name[0];

                $os = @$ros[$n][1];

                break;
            }
        }

        return trim ( $os );

    }

    public static function get_user_agent() {

        $u_agent = $_SERVER['HTTP_USER_AGENT'];

        if ( isset( $_SERVER ) ) {
               $u_agent = $_SERVER['HTTP_USER_AGENT'];
        } else {
            global $HTTP_SERVER_VARS;
            if ( isset( $HTTP_SERVER_VARS ) ) {
                $u_agent = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
            }
            else {
                global $HTTP_USER_AGENT;
                $u_agent = $HTTP_USER_AGENT;
            }
        }

        return $u_agent;

    }

    /**
     * Get Browser data from HTTP_USER_AGENT
     */
    public static function get_browser() {

        $u_agent = self::get_user_agent();

        $bname = $u_agent;
        $version= '';

        // Next get the name of the useragent yes seperately and for good reason
        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
        {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        }
        elseif(preg_match('/Firefox/i',$u_agent))
        {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        }
        elseif(preg_match('/Chrome/i',$u_agent))
        {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        }
        elseif(preg_match('/Safari/i',$u_agent))
        {
            $bname = 'Apple Safari';
            $ub = "Safari";
        }
        elseif(preg_match('/Opera/i',$u_agent))
        {
            $bname = 'Opera';
            $ub = "Opera";
        }
        elseif(preg_match('/Netscape/i',$u_agent))
        {
            $bname = 'Netscape';
            $ub = "Netscape";
        }

        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
        ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
                $version= $matches['version'][0];
            }
            else {
                $version= $matches['version'][1];
            }
        }
        else {
            $version= $matches['version'][0];
        }

        // check if we have a number
        if ($version==null || $version=="") {$version="?";}

        return array(
            'name'       => $bname,
            'version'    => $version,
            'pattern'    => $pattern
        );

    }

    /**
     * Ensures an ip address is both a valid IP and does not fall within
     * a private network range.
     */
    public static function validate_ip( $ip ) {

        if ( strtolower( $ip ) === 'unknown' ) return false;

        // generate ipv4 network address
        $ip = ip2long( $ip );

        // if the ip is set and not equivalent to 255.255.255.255
        if ( $ip !== false && $ip !== -1 ) {

            // make sure to get unsigned long representation of ip
            // due to discrepancies between 32 and 64 bit OSes and
            // signed numbers (ints default to signed in PHP)
            $ip = sprintf( '%u', $ip );
            // do private network range checking
            if ( $ip >= 0 && $ip <= 50331647 ) return false;
            if ( $ip >= 167772160 && $ip <= 184549375 ) return false;
            if ( $ip >= 2130706432 && $ip <= 2147483647 ) return false;
            if ( $ip >= 2851995648 && $ip <= 2852061183 ) return false;
            if ( $ip >= 2886729728 && $ip <= 2887778303 ) return false;
            if ( $ip >= 3221225984 && $ip <= 3221226239 ) return false;
            if ( $ip >= 3232235520 && $ip <= 3232301055 ) return false;
            if ( $ip >= 4294967040 ) return false;

        }

        return true;

    }

    /**
     * Get user IP address
     */
    public static function get_ip() {

        // check for shared internet/ISP IP
        if (!empty($_SERVER['HTTP_CLIENT_IP']) && self::validate_ip($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }

        // check for IPs passing through proxies
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {

            // check if multiple ips exist in var
            if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {

                $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                foreach ($iplist as $ip) {

                    if ( self::validate_ip( $ip ) ) {
                        return $ip;
                    }

                }
            } else {

                if ( self::validate_ip( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
                    return $_SERVER['HTTP_X_FORWARDED_FOR'];
                }

            }
        }
        if ( ! empty( $_SERVER['HTTP_X_FORWARDED'] ) && self::validate_ip( $_SERVER['HTTP_X_FORWARDED'] ) ) {
            return $_SERVER['HTTP_X_FORWARDED'];
        }
        if ( ! empty( $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'] ) && self::validate_ip( $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'] ) ) {
            return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        }
        if ( ! empty( $_SERVER['HTTP_FORWARDED_FOR'] ) && self::validate_ip( $_SERVER['HTTP_FORWARDED_FOR'] ) ) {
            return $_SERVER['HTTP_FORWARDED_FOR'];
        }
        if ( ! empty( $_SERVER['HTTP_FORWARDED'] ) && self::validate_ip( $_SERVER['HTTP_FORWARDED'] ) ) {
            return $_SERVER['HTTP_FORWARDED'];
        }


        // return unreliable ip since all else failed
        return $_SERVER['REMOTE_ADDR'];

    }

    public static function is_bot() {
        $bots = array( "google", "duckduckbot", "msnbot", "bingbot", "ask", "facebook", "yahoo", "addthis" );
        $patterns = implode( '|', $bots );
        return preg_match( '/' . $patterns . '/i', self::get_user_agent() );
    }

    public static function is_windows() {
        return (bool) strpos( self::get_user_agent(), 'Windows' );
    }

    public static function is_mac() {
        return ( ! self::is_ipad() && ! self::is_iphone() && (bool) strpos( self::get_user_agent(), 'Mac' ) );
    }

    public static function is_ipad() {
        // Mozilla/5.0 (iPad; CPU OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B137 Safari/601.1
        $is_ipad = (bool) strpos( self::get_user_agent(), 'iPad' );
        if ( $is_ipad )
            return true;
        else return false;
    }

    public static function is_iphone() {
        // Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B137 Safari/601.1
        return ( ! self::is_ipad() && ( (bool) strpos( self::get_user_agent(), 'iPhone' ) ) );
    }

    public static function is_ios() { // if the user is on any iOS Device
        return ( self::is_iphone() || self::is_ipad() );
    }

    public static function is_blackberry() {
        return (bool) strpos( self::get_user_agent(), 'BlackBerry' );

    }

    private static function is_mobile_or_phone() {
        return (bool) strpos( self::get_user_agent(), 'Mobile' ) || (bool) strpos( self::get_user_agent(), 'Phone' );
    }

    public static function is_windows_mobile() {
        return ( self::is_windows() && self::is_mobile_or_phone() );
    }

    public static function is_windows_desktop() {
        return ( self::is_windows() && ! self::is_mobile_or_phone() );
    }

    public static function is_windows_tablet() {
        $is_tablet = (bool) strpos( self::get_user_agent(), 'Tablet' );
        return ( self::is_windows() && $is_tablet );
    }

    public static function is_android() {
        return (bool) strpos( self::get_user_agent(), 'Android' );

    }

    public static function is_linux_desktop() { // if the user is on any iOS Device
        return ( (bool) strpos( self::get_user_agent(), 'Linux' ) && ! self::is_android() );
    }

    public static function is_android_mobile() {
        return ( self::is_android() && self::is_mobile_or_phone()  );
    }

    public static function is_android_tablet() { // detect only Android tablets
        global $is_iphone;
        return ( self::is_android() && ! self::is_android_mobile() );
    }

    public static function is_mobile() {
        global $is_iphone;
        return ( ! self::is_ipad() && ( self::is_android_mobile() || $is_iphone || self::is_iphone() || self::is_blackberry() || self::is_windows_mobile() ) );
    }

    public static function is_tablet() {
        return ( self::is_android_tablet() || self::is_ipad() );
    }

    public static function is_desktop() {
        return ( ! self::is_tablet() && ! self::is_mobile() );
    }

    public static function get_platform() {
        if ( self::is_mobile() ) {
            return 'Mobile';
        } elseif ( self::is_tablet() ) {
            return 'Tablet';
        } else {
            return 'Desktop';
        }
    }


}
endif;
