var months_names = [ 'Styczeń', 'Luty', 'Marzec', 'Kwiecień', 'Maj', 
    'Czerwiec', 'Lipiec', 'Sierpień', 'Wrzesień', 'Październik', 
    'Listopad', 'Grudzień' ];

    var day_name = [ 'Niedziela', 'Poniedziałek', 'Wtorek', 'Środa', 
                    'Czwartek', 'Piątek', 'Sobota'];
function Time()
{
    var data = new Date();
    var day = data.getDate();

    var month = data.getMonth();
    var year = data.getFullYear();
    var hour = data.getHours();
    var min = data.getMinutes();
    var sec = data.getSeconds();

    if(min < 10) min = '0' + min;
    if(sec < 10) sec = '0' + sec;
    var date_time = day_name[data.getDay()] + ' ' + day + ' ' 
        + months_names[month] + ' ' + year + ' ' + hour + ':' + min + ':' + sec;
    document.getElementById('Timer').innerHTML=date_time;
}
setInterval('Time()', 1000);

   

