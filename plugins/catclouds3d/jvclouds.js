// Установить куки
function setCookie(name, value) {
      var valueEscaped = escape(value);
      var expiresDate = new Date();
      expiresDate.setTime(expiresDate.getTime() + 365 * 24 * 60 * 60 * 1000); // срок - 1 год, но его можно изменить
      var expires = expiresDate.toGMTString();
      var newCookie = name + "=" + valueEscaped + "; path=/; expires=" + expires;
      if (valueEscaped.length <= 4000) document.cookie = newCookie + ";";
}
// Получить куки
function findCookie(szName) 
{
  var i = 0;
  var nStartPosition = 0;
  var nEndPosition = 0;  
  var szCookieString = document.cookie;  

  while(i <= szCookieString.length) 
  {
    nStartPosition = i;
    nEndPosition = nStartPosition + szName.length;

    if(szCookieString.substring( 
        nStartPosition,nEndPosition) == szName) 
    {
      nStartPosition = nEndPosition + 1;
      nEndPosition = 
        document.cookie.indexOf(";",nStartPosition);

      if(nEndPosition < nStartPosition)
        nEndPosition = document.cookie.length;

      return document.cookie.substring( 
          nStartPosition,nEndPosition);  
      break;    
    }
    i++;  
  }
  return "";
}
