<!DOCTYPE html>
<html>
<head>
    <title>Email</title>
    <style>
    	* {
    		margin: 0;
    		padding: 0;
    		outline: none;
    		box-sizing: border-box;
    	}
    	body {
    		font-family: Arial,'Helvetica Neue',Helvetica,sans-serif;
    		font-size: 14px;
    	}
    	ul {
    		list-style: none;
    	}
    	.container {
    		width: 600px;
    		margin: 0 auto;
    	}
    	.header {
    		width: 100%;
    		float: left;
    		clear: both;
    		padding: 10px 0;
    		background: linear-gradient(to right, #1da64c, #69be34);
    	}
        .header-logo {
            text-align: center;
        }
        .header-logo a {
            display: block;
            color:  #ffffff;
            line-height: 30px;
            font-size: 22px;
            font-weight: bold;
        }
    	.header-image {
    		width: 100%;
    		float: left;
    		height: 50px;
    	} 
    	.header-image img {
    		max-width: 100%;
    		max-height: 100%;
    		object-fit: contain;
    		margin: 0 auto;
    		display: block;
    	}
    	.main {
    		width: 100%;
    		float: left;
    		clear: both;
    		margin: 0;
            padding: 15px 0;
            background: #e9edee;
    	}
    	.footer {
    		width: 100%;
    		float: left;
    		clear: both;
    		background: #18214b;
    		padding: 10px 0;
    	}
    	.footer-content {
			width: 100%;
    		float: left;
    		clear: both;
    	}
    	.footer-content ul {
    		width: 100%;
    		float: left;
    		clear: both;
    	}
    	.footer-content ul li {
    		color: #ffffff;
    		line-height: 25px;
            margin-bottom: 5px;
    	}
        .footer-content ul li:last-child {
            margin-bottom: 0;
        }
    	.footer-content ul li img {
            float: left;
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            width: 15px;
            height: 15px;
            margin: 4px 10px 6px 0;
    	}
        .footer-content ul li p {
            color: #ffffff;
        }
    	.footer-content ul li a {
    		color: #ffffff;
    	}
    	.chanhtuoi-email {
    		width: 100%;
    		float: left;
    		background: #ffffff;
            border: 1px solid #cccccc;
    		padding: 10px;
    	}
        .css-content {
            width: 100%;
            float: left;
            line-height: 22px;
        }
        .css-content h3 {
            width: 100%;
            float: left;
            font-size: 15px;
            line-height: 22px;
            margin-bottom: 5px;
        }
        .css-content p {
            margin-bottom: 5px;
        }
        .css-content a {
            color: #01be42;
            font-weight: bold;
            text-decoration: none;
        }
        .css-content img {
            max-width: 100%;
            max-height: 100%;
            display: block;
            margin: 0 auto;
        }
        .css-content ul {
            padding-left: 20px;
            list-style-type: circle;
            margin-bottom: 5px;
        }
        .css-content ul li {
            margin-bottom: 3px;
        }
        .css-content .btn {
            display: inline-block;
            padding: 8px 30px;
            text-align: center;
            border-radius: 5px;
            transition: 0.2s;
            border: none;
            margin: 10px 0;
        }
        .css-content .btn-primary {
            background: #1da64c;
            color: #ffffff;
        }
        .css-content .btn-full {}
        .css-content .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="header-logo"><a href="javascript:;" target="_blank">DreamTeamCMS</a></div>
        </div>
    </div>
    <div class="main">
        @yield('content')
    </div>
    <div class="footer">
        <div class="container">
            <div class="footer-content">
                <ul>
                    <li><a href="https://dreamteam.com">{{__('AdminUser::admin.email.name_company')}}</a></li>
                    <li>99 Nguyễn Phong Sắc, Cầu Giấy, Hà Nội</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>