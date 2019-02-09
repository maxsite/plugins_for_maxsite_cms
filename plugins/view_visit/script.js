	function setCookie(name, value) {
var valueEscaped = escape(value);
var expiresDate = new Date();
expiresDate.setTime(expiresDate.getTime() + 365 * 24 * 60 * 60 * 1000); // срок Ц 1 год, но его можно изменить
var expires = expiresDate.toGMTString();
var newCookie = name + "=" + valueEscaped + "; path=/; expires=" + expires;
if (valueEscaped.length <= 4000) document.cookie = newCookie + ";";
}
if (self.screen) {
width = screen.width;
height = screen.height;
resol = width+"*"+height;
}else{		resol = "bad";	}
setCookie("resol", resol);