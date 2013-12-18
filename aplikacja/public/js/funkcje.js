function dataUrodzeniaPESEL(pesel){
    
    var regExp = new RegExp(/^[0-9]{11}$/);
    if(regExp.test(pesel) == false) return 0;
    
    var r = pesel.substr(0, 2);
    var m = pesel.substr(2, 2);
    var d = pesel.substr(4, 2);
    var prefixRok = "19";
    
    var kodMiesiac = parseInt(m[0]);
    
    if(kodMiesiac == 8 || kodMiesiac == 9) prefixRok = "18";
    else if(kodMiesiac == 2 || kodMiesiac == 3) prefixRok = "20";
    else if(kodMiesiac == 4 || kodMiesiac == 5) prefixRok = "21";
    else if(kodMiesiac == 6 || kodMiesiac == 7) prefixRok = "22";
    
    if(kodMiesiac % 2 == 0) m = "0" + m[1];
    else m = "1" + m[1];
    
    var d = new Date(prefixRok + r + "-" + m + "-" + d);
    
    if((d.valueOf())+"" != "NaN"){
        return d;
    }
    
    return 0;
}


function walidacjaPESEL(pesel){
    
    var regExp = new RegExp(/^[0-9]{11}$/);
    if(regExp.test(pesel) == false) return 0;
    
    var data = dataUrodzeniaPESEL(pesel);
    
    if(data != 0){
        
       var suma = 1*parseInt(pesel[0]) + 3*parseInt(pesel[1]) + 7*parseInt(pesel[2]) + 9*parseInt(pesel[3]) + 1*parseInt(pesel[4]) + 3*parseInt(pesel[5]) + 7*parseInt(pesel[6]) + 9*parseInt(pesel[7]) + 1*parseInt(pesel[8]) + 3*parseInt(pesel[9]);
       
       var kontrolna = suma % 10;
       
       if(kontrolna == 0) kontrolna = 0;
       else kontrolna = 10 - kontrolna;
       
       if(kontrolna == parseInt(pesel[pesel.length-1])) return 1;
       
       return 0;
    }
    

    return 0;
}
