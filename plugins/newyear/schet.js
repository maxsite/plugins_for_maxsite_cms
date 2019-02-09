function $mov(id)
{
	return document.getElementById(id);
}

function Timer()
{
    
    var now = new Date();
    var newYear = now.getFullYear() + 1;
    var firstJan = new Date(newYear, 0, 1, 0, 0, 0);
    var sec = (firstJan.getTime() - now.getTime()) / 1000;
    sec = Math.round(sec);
    $mov("valSec").innerHTML = sec;
    var ost1 = sec % 10;
    var ost2 = sec % 100;
    var outen;
    if ((ost1 == 1) && (ost2 != 11))
        outen = " секунда ";
    else if (((ost1 == 2) && (ost2 != 12)) || ((ost1 == 3) && (ost2 != 13)) || ((ost1 == 4) && (ost2 != 14)))
        outen = " секунды ";
    else 
        outen = " секунд ";
    $mov("descSec").innerHTML = outen;
 
    var minutes = Math.round(sec / 60);
    $mov("valMin").innerHTML = minutes;
    ost1 = minutes % 10;
    ost2 = minutes % 100;
    if ((ost1 == 1) && (ost2 != 11))
        outen = " минута ";
    else if (((ost1 == 2) && (ost2 != 12)) || ((ost1 == 3) && (ost2 != 13)) || ((ost1 == 4) && (ost2 != 14)))
        outen = " минуты ";
    else 
        outen = " минут ";
    $mov("descMin").innerHTML = outen;
 
    var hour = Math.round(minutes / 60);
    $mov("valHour").innerHTML = hour;
    ost1 = hour % 10;
    ost2 = hour % 100;
    if ((ost1 == 1) && (ost2 != 11))
        outen = " час ";
    else if (((ost1 == 2) && (ost2 != 12)) || ((ost1 == 3) && (ost2 != 13)) || ((ost1 == 4) && (ost2 != 14)))
        outen = " часа ";
    else 
        outen = " часов ";
    $mov("descHour").innerHTML = outen;
 
    var day = Math.round(hour / 24);
    $mov("valDay").innerHTML = day;
    ost1 = day % 10;
    ost2 = day % 100;
    if ((ost1 == 1) && (ost2 != 11))
        outen = " день ";
    else if (((ost1 == 2) && (ost2 != 12)) || ((ost1 == 3) && (ost2 != 13)) || ((ost1 == 4) && (ost2 != 14)))
        outen = " дня ";
    else 
        outen = " дней ";
    $mov("descDay").innerHTML = outen;
    
}
setInterval("Timer()", 1000);