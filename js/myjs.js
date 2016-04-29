/**
 *@an easy ajax lib
 *@Author:Yaoh
 *@Version: v2.0 2009/9
 */
//跳到網址
function jumpto(url){
window.location=url;
}
function open_new(url,title){
　window.open(url, title, config='height=600,width=800');
}
//處理sj
var request = null;
function bi(id){
	return document.getElementById(id);
}
//以id取得物件
function bii(id){
	document.getElementById(id).value;
	return document.getElementById(id).value;
}
function sjg(url,id){
	return httpRequest('get',url,true,function(){r2id(id);});
}
function sjgn(url,id){
	return httpRequest('get',url,true,function(){r2idn(id);});
}
function r2id(id){
	if(request.readyState==4 && request.status==200){
		bi(id).innerHTML=request.responseText;
	}else{
		bi(id).innerHTML="<span>loading..........</span>";
	}
}
function r2idn(id){
	if(request.readyState==4 && request.status==200){
		bi(id).innerHTML=request.responseText;
	}
}
function sjp(url,id,arg){
	return httpRequest('post',url,true,function(){r2id(id);},arg);
}
function sjpn(url,id,arg){
	return httpRequest('post',url,true,function(){r2idn(id);},arg);
}
function sjf(url,id,form_id){
		sjp(url,id,gfv(bi(form_id)));
}
function sjfn(url,id,form_id){
		sjpn(url,id,gfv(bi(form_id)));
}	
/**
 *用途擷取某一表單的值並轉成query string
 *@form : 表單物件名稱
 *@return : string 用來裝入ajax的字串
 */
function gfv(form){
 var arQuery = [];
 var fe=form.elements;
    for (var i in fe) {
        if (fe.hasOwnProperty(i)) {
			if (typeof fe[i].name!='undefined'){
					if(fe[i].type.toLowerCase()=='radio' && fe[i].checked!=true)continue;
					arQuery.push(fe[i].name+'='+encodeURIComponent(fe[i].value));
				}
			}
        }
    return arQuery.join("&");
}
function httpRequest(reqType,url,asynch,respHandle){
	if(window.XMLHttpRequest){
		request = new XMLHttpRequest();
	}else if(window.ActiveXObject){
		request = new ActiveXObject("Msxml2.XMLHTTP");
		if(!request){
			request= new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	if(request){
			if(reqType.toLowerCase()!="post"){
				initReq(reqType,url,asynch,respHandle);
			}else{
				var args=arguments[4];
				if(args != null && args.length >0){
					initReq(reqType,url,asynch,respHandle,args);
				}
			}
	}else{
		alert("Please upgrade your browser!!!!!");
	}
}

function initReq(reqType,url,asynch,respHandle){
	try{
		request.onreadystatechange=respHandle;
		request.open(reqType,url,asynch);
		if(reqType.toLowerCase()=="post"){
			request.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
			request.send(arguments[4]);
		}else{
			request.send(null);
		}
	}catch (errv){
		alert("Error happen~"+errv.message);
	}
}
		
