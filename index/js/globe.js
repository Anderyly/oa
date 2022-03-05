function httpCurl(url, postData) {
	var token = sessionStorage.getItem("token");
	if (token == "" || token == undefined) {
		token = ""
	}
	postData['token'] = token
	// return new Promise(function(resolve, reject) {
	let row;
	$.ajax({
		url: sessionStorage.getItem("url") + url,
		type: sessionStorage.getItem("method"),
		dataType: 'json',
		data: postData,
		async: false,
		success: function(data) {
			if (data.code == 200) {
				// console.log(data)
				// resolve(data);
				row = data;
			} else if (data.code == 1001 || data.code == 1003 || data.code == 1002) {
				layer.msg(data.msg, {
					icon: 5,
					time: 2000
				}, function() {
					sessionStorage.removeItem("token");
					sessionStorage.removeItem("user");
					window.localStorage.setItem("lockscreen", true);
				});
			} else if (data.code == 1020) {
				layer.msg(data.msg, {
					icon: 5,
					time: 2000
				}, function() {
				});
			} else {
				// resolve(data);
				row = data;
			}
		},
	});
	return row;
	// });
}

function auth(id) {
	let user = sessionStorage.getItem("user")
	user = JSON.parse(user)
	let root = user.root
	let au = [];
	au = root.split(',');

	if (au.indexOf(id.toString()) == -1) {
		return false
	} else {
		return true;
	}
	
}


function getUrlKey(name) {
	return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.href) || [,
		""
	])[1].replace(/\+/g, '%20')) || null
}
