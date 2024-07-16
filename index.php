<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>物料信息编码生成器</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden; /* 防止整个页面滚动 */
        }

        .text-center {
            text-align: center;
            width: 100%;
            position: fixed;
            top: 0;
            background-color: white;
            z-index: 1000;
            padding: 10px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .sidebar {
            position: fixed;
            top: 60px; /* 标题的高度 */
            left: 0;
            width: 20%; /* 改为相对单位 */
            height: calc(100% - 60px);
            background-color: #f8f9fa;
            padding: 2%; /* 改为相对单位 */
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            overflow-y: auto; /* 允许侧边栏内部滚动 */
        }

        .main-content {
            display: flex;
            /* align-items: center; */
            justify-content: center;
            width: calc(100% - 24%); /* 改为相对单位 */
            margin-left: 17%; /* 改为相对单位 */
            margin-top: 100px; /* 标题的高度 */
            height: calc(100% - 100px); /* 确保主内容区域高度 */
            flex-direction: row; /* 改为水平布局 */
            box-shadow: -7px 7px 5px 0 rgba(0, 0, 0, 0.5),1px -1px 1px 0 rgba(0, 0, 0, 0.2);
            border-radius: 10px 10px 10px 10px;
            overflow: hidden; /* 防止主内容区域滚动 */
        }

        .input-container {
            display: none;
            flex-direction: row;
            flex-wrap: wrap;
            align-items: flex-start;
            justify-content: flex-start;
            padding: 2%; /* 改为相对单位 */
            box-sizing: border-box;
            width: 100%; /* 改为50%宽度 */
            position: relative;
            border: 1px solid #ccc;
            height: 100%; /* 设置默认高度为现在的一半 */
            overflow-y: auto; /* 添加垂直滚动条 */
            margin-left: 0%; /* 添加左侧间距 */
            margin-top: 0%; /* 增加顶部间距 */
            /* gap: 10px; 设置间距 */
        }
        .search-container {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: flex-start;
            padding: 2%; /* 改为相对单位 */
            box-sizing: border-box;
            width: 100%; /* 改为50%宽度 */
            position: relative;
            border: 1px solid #ccc;
            height: 100%; /* 保持现有高度 */
            overflow-y: auto; /* 添加垂直滚动条 */
            margin-left: 0%; /* 添加左侧间距 */
            margin-top: 0%; /* 增加顶部间距 */
        }

        .search-header {
            display: flex;
            justify-content: space-between;
            width: 100%;
            height: 40px;
            position: sticky;
            top: 0; /* 固定在顶部 */
            background-color: #f0f0f0; /* 与输入框背景颜色一致 */
            z-index: 999;
            padding-bottom: 0; /* 移除底部内边距 */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease; /* 添加过渡效果 */
        }

        .search-header:hover {
            background-color: #e0e0e0; /* 悬停时的背景颜色 */
        }

        .search-header input {
            flex-grow: 1; /* 使输入框占据剩余空间 */
            border: none;
            outline: none; /* 移除聚焦时的默认边框 */
            padding: 10px; /* 添加内边距以改善视觉效果 */
            background-color: transparent; /* 使输入框背景透明 */
        }

        .search-header button {
            flex-shrink: 0; /* 防止按钮缩小 */
            border-radius: 0; /* 移除按钮的圆角 */
            background-color: #007bff; /* 按钮背景颜色 */
            color: white; /* 按钮文字颜色 */
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease; /* 添加过渡效果 */
            height: 100%; /* 使按钮高度占满 .search-header */
            width: 100px; /* 设置按钮的宽度 */
        }

        .search-header button:hover {
            background-color: #0056b3; /* 悬停时的按钮背景颜色 */
        }
        .searchResults {
            margin-top: 20px;
            text-align: left; /* 确保搜索结果靠左对齐 */
            width: 100%;
            background-color: #f0f0f0; /* 添加背景颜色 */
            padding: 10px;
            box-sizing: border-box;
            overflow-y: auto;
            max-height: calc(100vh - 180px); /* 设置最大高度 */
        }
        .searchResults strong {
            font-weight: bold; /* 确保关键字加粗 */
        }
        .form-group {
            display: flex;
            flex-direction: column;
            margin: 5px;
            width: calc(33% - 20px); /* 每行三个输入框，减去间距 */
            box-sizing: border-box;
        }

        .propertyLabel {
            font-weight: bold;
            font-size: 15px;
            margin-bottom: 5px; /* 确保标签在输入框的顶部 */
            /* text-decoration: underline; */
        }
        .input-field {
            padding: 3%;
            border: 2px solid;
            border-radius: 6px;
            font-size: 14px;
            width: 100%; /* 确保输入框占据整个宽度 */
            box-sizing: border-box;
        }
        .sidebar-header {
            position: sticky; /* 使标题固定在顶部 */
            top: 0; /* 固定在顶部 */
            background-color: #f8f9fa; /* 与侧边栏背景色相同，避免滚动时出现空白 */
            z-index: 1; /* 确保标题在内容之上 */
            padding-bottom: 10px; /* 增加一些间距 */
            border-bottom: 1px solid #dee2e6; /*添加分隔线 */
        }

        .components-list {
            list-style: none;
            padding: 0;
        }

        .component-group {
            position: relative;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .component-group span {
            font-weight: bold;
            cursor: pointer;
            display: block;
        }

        .component-group span.active {
            color: #007bff;
        }

        .subcomponents-list {
            list-style: none;
            padding: 0;
            margin-left: 20px;
            display: none;
        }

        .subcomponents-list li {
            padding-left: 10px;
            margin-bottom: 5px;
            cursor: pointer;
        }

        .subcomponents-list li.active {
            color: #007bff;
        }

        .sidebar-content {
            flex-grow: 1;
            overflow-y: auto; /* 允许侧边栏内容滚动 */
        }

        .someButton {
            display: block;
            width: 100%; /* 按钮宽度与侧边栏宽度相同 */
            padding: 10px 20px;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s ease;
            border-radius: 10px;
            margin-top: 20px; /* 确保按钮与内容之间有间距 */
        }

        .someButton:hover {
            background-color: #0056b3;
        }

        .sidebar::after {
            content: '';
            display: block;
            height: 1px;
            background-color: #dee2e6;
            margin: 20px 0;
            position: absolute;
            bottom: 60px; /* 按钮的高度加上 margin */
            left: 20px;
            right: 20px;
        }
        .button:hover{
            background-color: #0056b3;
        }
        .code-display-container {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .code-display {
            margin-right: 10px;
        }
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }

        .pagination button {
            margin: 0 10px;
        }
                
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: static;
            }
            .main-content {
                width: 100%;
                margin-left: 0;
                margin-top: 0;
            }
            .input-container, .search-container {
                width: 100%;
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            .sidebar {
                width: 30%;
            }
            .main-content {
                width: 70%;
                margin-left: 30%;
            }
        }
    </style>
</head>
<body>
    <h1 class="text-center">物料信息编码生成</h1>

    <div class="sidebar">
        <div class="sidebar-header">
            <h2>请选择物料类型</h2>
        </div>
        <div class="sidebar-content">
            <ul class="components-list">
                <!-- 物料类型列表将动态生成 -->
            </ul>
        </div>
        <button id="someButton" class="someButton">生成物料编码</button>
    </div>

    <div class="main-content">
        <div class="input-container">
            <!-- 动态生成的输入框将插入到这里 -->
        </div>

        <div class="search-container">
            <div class="search-header">
                <input type="text" id="searchInput" placeholder="输入参数信息进行查询">
                <button id="searchButton" class="searchButton">搜索</button>
            </div>
            <div id="searchResults" class="searchResults"></div>
            <div id="pagination" class="pagination">
                <button id="prevPage" disabled>上一页</button>
                <span id="pageInfo"></span>
                <button id="nextPage">下一页</button>
            </div>
        </div>
    </div>

    <script>
        const components = {
            '电阻': ['贴片电阻', '插装电阻', '压敏电阻','热敏电阻','电位器'],
            '电容': ['陶瓷电容', '贴片电容'],
            '电感': ['固定电感', '可变电感'],
            '复合开关': [],'采集器': [],'壳表':['单相壳表','三相壳表','负控壳表'],'配件包':[],'智能电容器':[],'表箱':[],'连接器':[],
            '继电器':[],'电流互感器':[],'电流传感器':[],'充电桩':[],'智能井盖':[],'故障指示器':[],'断路跳闸触发控制器':[],'其他成品':[],
            '盖、壳、帽、套':['罩盖','温度检测外壳','上盖','上壳','后盖','卡套','铅封帽','端子帽','防尘盖','控制盒盖','电池盖','上透明盖','开关帽','按钮帽','红杠套','侧盖','锁具面盖'],
            '分流器':[],'模块架':[],'按钮':[],'导光柱':[]
        };
        const subTypeProperties = {
            '贴片电阻': ['阻值', '精度', '功率', '温度系数', '封装尺寸', '最大工作电压', '绝缘电阻性能'],
            '贴片电容': ['电容量', '精度', '额定电压', '封装尺寸', '温度系数'],
            '复合开关': ['型号', '额定电流', '额定电压'],
            '采集器': ['型号', '通信接口', '数据采集频率']
            // 请继续为其他子类型定义属性...
        };
        const sidebarList = document.querySelector('.sidebar ul');
        Object.keys(components).forEach(type => {
            const group = document.createElement('li');
            group.classList.add('component-group');
            const typeSpan = document.createElement('span');
            typeSpan.textContent = type;
            typeSpan.addEventListener('click', function() {
                // 折叠所有其他一级列表
                document.querySelectorAll('.subcomponents-list').forEach(list => {
                    if (list !== this.nextElementSibling) {
                        list.style.display = 'none';
                    }
                });

                // 清除所有二级列表的激活状态
                document.querySelectorAll('.subcomponents-list li').forEach(l => l.classList.remove('active'));

                if (components[type].length === 0) {
                    // 处理没有二级列表的情况
                    const inputContainer = document.querySelector('.input-container');
                    const searchContainer = document.querySelector('.search-container');
                    searchContainer.style.display = 'none';
                    inputContainer.style.display = 'flex';
                    while (inputContainer.firstChild) {
                        inputContainer.removeChild(inputContainer.firstChild);
                    }

                    // 添加申请人输入框
                    const applicantGroup = document.createElement('div');
                    applicantGroup.classList.add('form-group');
                    const applicantLabel = document.createElement('label');
                    applicantLabel.textContent = '申请人：';
                    applicantLabel.classList.add('propertyLabel');
                    const applicantInput = document.createElement('input');
                    applicantInput.classList.add('input-field'); // 添加单独的类名
                    applicantInput.type = 'text';
                    applicantInput.name = 'applicant';
                    applicantInput.placeholder = '请输入申请人';
                    applicantGroup.appendChild(applicantLabel);
                    applicantGroup.appendChild(applicantInput);
                    inputContainer.appendChild(applicantGroup);

                    // 添加申请时间输入框
                    const applicationTimeGroup = document.createElement('div');
                    applicationTimeGroup.classList.add('form-group');
                    const applicationTimeLabel = document.createElement('label');
                    applicationTimeLabel.textContent = '申请时间：';
                    applicationTimeLabel.classList.add('propertyLabel');
                    const applicationTimeInput = document.createElement('input');
                    applicationTimeInput.classList.add('input-field'); // 添加单独的类名
                    applicationTimeInput.type = 'text';
                    applicationTimeInput.name = 'applicationTime';
                    applicationTimeInput.placeholder = '请输入申请时间';
                    applicationTimeInput.value = new Date().toISOString().split('T')[0]; // 获取当前日期
                    applicationTimeGroup.appendChild(applicationTimeLabel);
                    applicationTimeGroup.appendChild(applicationTimeInput);
                    inputContainer.appendChild(applicationTimeGroup);

                    const properties = subTypeProperties[type] || [];
                    properties.forEach(property => {
                        const propertyGroup = document.createElement('div');
                        propertyGroup.classList.add('form-group');
                        const propertyLabel = document.createElement('label');
                        propertyLabel.textContent = `${property}:`;
                        propertyLabel.classList.add('propertyLabel');
                        const input = document.createElement('input');
                        input.classList.add('input-field'); // 添加单独的类名
                        input.placeholder = `请输入${property}`;
                        input.type = 'text';
                        input.name = `${property.replace(/\s/g, '')}`;
                        propertyGroup.appendChild(propertyLabel);
                        propertyGroup.appendChild(input);
                        inputContainer.appendChild(propertyGroup);
                    });
                    
                    // 添加激活状态
                    document.querySelectorAll('.component-group span').forEach(s => s.classList.remove('active'));
                    this.classList.add('active');
                } else {
                    // 处理有二级列表的情况
                    const subList = this.nextElementSibling;
                    subList.style.display = subList.style.display === 'block' ? 'none' : 'block';
                    document.querySelectorAll('.component-group span').forEach(s => s.classList.remove('active'));
                    this.classList.add('active');
                }
            });
            group.appendChild(typeSpan);
            const subList = document.createElement('ul');
            subList.classList.add('subcomponents-list');
            components[type].forEach(subType => {
                const item = document.createElement('li');
                item.textContent = subType;
                item.addEventListener('click', function() {
                    const inputContainer = document.querySelector('.input-container');
                    const searchContainer = document.querySelector('.search-container');
                    searchContainer.style.display = 'none';
                    inputContainer.style.display = 'flex';
                    const inputValue = this.textContent;
                    while (inputContainer.firstChild) {
                        inputContainer.removeChild(inputContainer.firstChild);
                    }
                    const properties = subTypeProperties[inputValue] || [];
                    const placeholders = [];

                    // 添加申请人输入框
                    const applicantGroup = document.createElement('div');
                    applicantGroup.classList.add('form-group');
                    const applicantLabel = document.createElement('label');
                    applicantLabel.textContent = '申请人：';
                    applicantLabel.classList.add('propertyLabel');
                    const applicantInput = document.createElement('input');
                    applicantInput.classList.add('input-field'); // 添加单独的类名
                    applicantInput.type = 'text';
                    applicantInput.name = 'applicant';
                    applicantInput.placeholder = '请输入申请人';
                    applicantGroup.appendChild(applicantLabel);
                    applicantGroup.appendChild(applicantInput);
                    inputContainer.appendChild(applicantGroup);

                    // 添加申请时间输入框
                    const applicationTimeGroup = document.createElement('div');
                    applicationTimeGroup.classList.add('form-group');
                    const applicationTimeLabel = document.createElement('label');
                    applicationTimeLabel.textContent = '申请时间：';
                    applicationTimeLabel.classList.add('propertyLabel');
                    const applicationTimeInput = document.createElement('input');
                    applicationTimeInput.classList.add('input-field'); // 添加单独的类名
                    applicationTimeInput.type = 'text';
                    applicationTimeInput.name = 'applicationTime';
                    applicationTimeInput.placeholder = '请输入申请时间';
                    applicationTimeInput.value = new Date().toISOString().split('T')[0]; // 获取当前日期
                    applicationTimeGroup.appendChild(applicationTimeLabel);
                    applicationTimeGroup.appendChild(applicationTimeInput);
                    inputContainer.appendChild(applicationTimeGroup);
                    
                    properties.forEach(property => {
                        const propertyGroup = document.createElement('div');
                        propertyGroup.classList.add('form-group');
                        const propertyLabel = document.createElement('label');
                        propertyLabel.textContent = `${property}:`;
                        propertyLabel.classList.add('propertyLabel');
                        const input = document.createElement('input');
                        input.classList.add('input-field'); // 添加单独的类名
                        input.placeholder = `请输入${property}`;
                        input.classList.add('form-group');
                        input.type = 'text';
                        input.name = `${inputValue}_${property.replace(/\s/g, '')}`;
                        propertyGroup.appendChild(propertyLabel);
                        propertyGroup.appendChild(input);
                        inputContainer.appendChild(propertyGroup);
                    });
                    document.querySelectorAll('.subcomponents-list li').forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                });
                subList.appendChild(item);
            });
            group.appendChild(subList);
            sidebarList.appendChild(group);
        });
        document.querySelectorAll('.subcomponents-list').forEach(list => {
            list.style.display = 'none';
        });

        document.getElementById('someButton').addEventListener('click', function() {
            const inputContainer = document.querySelector('.input-container');
            const inputs = inputContainer.querySelectorAll('.input-field');
            const type = document.querySelector('.component-group span.active')?.textContent;
            const subType = document.querySelector('.subcomponents-list li.active')?.textContent;

            if (!type) {
                alert('请先选择物料类型');
                return;
            }

            // 检查申请人、申请时间是否有内容
            let hasApplicant = false;
            let hasApplicationTime = false;

            inputs.forEach(input => {
                if (input.name === 'applicant' && input.value.trim() !== '') {
                    hasApplicant = true;
                }
                if (input.name === 'applicationTime' && input.value.trim() !== '') {
                    hasApplicationTime = true;
                }
            });

            if (!hasApplicant || !hasApplicationTime) {
                alert('请至少输入申请人、申请时间信息');
                return;
            }

            const properties = Array.from(inputs).map(input => input.value);
            const placeholders = Array.from(inputs).map(input => input.placeholder.replace('请输入', '').replace('信息', ''));
            console.log("placeholders2",placeholders);
            // 获取备案号
            fetch('get_record_number.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const recordNumber = data.recordNumber;
                    console.log(recordNumber);
                    const { fullCode, codePrefix } = generateCode(type, subType, properties, placeholders, recordNumber);

                    // 清除之前生成的编码信息和复制按钮
                    const existingCodeDisplayContainer = inputContainer.querySelector('.code-display-container');
                    if (existingCodeDisplayContainer) {
                        inputContainer.removeChild(existingCodeDisplayContainer);
                    }

                    const codeDisplayContainer = document.createElement('div');
                    codeDisplayContainer.classList.add('code-display-container');

                    const codeDisplay = document.createElement('div');
                    codeDisplay.classList.add('code-display');
                    codeDisplay.textContent = `生成的编码: ${codePrefix.replace(/-/g, '')}`;
                    codeDisplay.style.marginTop = '20px';
                    codeDisplayContainer.appendChild(codeDisplay);

                    // 添加复制按钮
                    const copyButton = document.createElement('button');
                    copyButton.textContent = '复制编码';
                    copyButton.style.marginTop = '10px';
                    copyButton.style.marginLeft = '10px';
                    copyButton.style.padding = '5px 10px';
                    copyButton.style.borderRadius = '5px';
                    copyButton.addEventListener('click', function() {
                        const tempInput = document.createElement('input');
                        tempInput.style.position = 'absolute';
                        tempInput.style.left = '-9999px';
                        tempInput.value = codePrefix.replace(/-/g, '');
                        document.body.appendChild(tempInput);
                        tempInput.select();
                        document.execCommand('copy');
                        document.body.removeChild(tempInput);
                        alert('编码已复制到剪贴板');
                    });
                    codeDisplayContainer.appendChild(copyButton);
                    inputContainer.appendChild(codeDisplayContainer);
                    console.log("properties",properties);
                    console.log("fullCode",fullCode);
                    // 发送数据到后端API
                    fetch('save_code.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            type: type,
                            sub_type: subType || '',
                            properties: properties,
                            code: fullCode,
                            record_number: recordNumber
                        })
                    })
                    .then(response => response.text()) // 使用 text() 方法捕获原始响应内容
                    .then(text => {
                        console.log('Raw response:', text); // 记录原始响应内容
                        try {
                            const data = JSON.parse(text); // 尝试解析 JSON
                            if (data.status !== 'success') {
                                alert(data.message);
                            } else {
                                alert('编码已成功保存');
                            }
                        } catch (e) {
                            console.error('JSON parsing error:', e); // 记录 JSON 解析错误
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        alert('保存编码时出错');
                    });
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('获取备案号时出错');
            });
        });

        document.getElementById('searchButton').addEventListener('click', function() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();

            // 发送搜索请求到后端API
            fetch(`search_code.php?searchInput=${searchInput}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const searchResultsContainer = document.getElementById('searchResults');
                    searchResultsContainer.innerHTML = '';

                    if (data.data.length === 0) {
                        searchResultsContainer.innerHTML = '<p>没有找到匹配的结果</p>';
                    } else {
                        let currentPage = 1;
                        const resultsPerPage = 5;
                        const totalPages = Math.ceil(data.data.length / resultsPerPage);

                        function displayResults(page) {
                            searchResultsContainer.innerHTML = '';
                            const start = (page - 1) * resultsPerPage;
                            const end = start + resultsPerPage;
                            const pageResults = data.data.slice(start, end);

                            pageResults.forEach(result => {
                                const resultElement = document.createElement('div');
                                resultElement.innerHTML = `
                                    <p><strong>编码:</strong> ${result.code.split('-')[0].replace(/-/g, '')} <strong>类型:</strong> ${result.type} ${result.sub_type ? '<strong>子类型:</strong> ' + result.sub_type : ''}</p> 
                                    <table border="1" cellpadding="3" cellspacing="0">
                                        <thead>
                                        <tr>
                                            ${result.properties.map((property, index) => {
                                                const [key, value] = property.split(':');
                                                return `<th><strong>${key}</strong></th> `;
                                            }).join('')}
                                        </tr>
                                        <thead>
                                        <tbody>
                                        <tr>
                                            ${result.properties.map((property, index) => {
                                                const [key, value] = property.split(':');
                                                return `<td>${value}</td>`;
                                            }).join('')}
                                        </tr>
                                        </tbody>
                                    </table>
                                    <br>
                                    <div class="action-buttons">
                                        <button class="delete-button" data-code="${result.code}">删除</button>
                                    </div>
                                    <hr>
                                `;
                                searchResultsContainer.appendChild(resultElement);
                            });

                            // 添加删除按钮的事件监听器
                            document.querySelectorAll('.delete-button').forEach(button => {
                                button.addEventListener('click', function() {
                                    const code = this.getAttribute('data-code');
                                    if (confirm('确认删除此编码信息吗？')) {
                                        fetch(`delete_code.php?code=${code}`, {
                                            method: 'DELETE'
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.status === 'success') {
                                                alert('删除成功');
                                                // 重新加载搜索结果
                                                document.getElementById('searchButton').click();
                                            } else {
                                                alert(data.message);
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error:', error);
                                            alert('删除编码时出错');
                                        });
                                    }
                                });
                            });

                            document.getElementById('pageInfo').textContent = `第 ${page} 页，共 ${totalPages} 页`;
                            document.getElementById('prevPage').disabled = page === 1;
                            document.getElementById('nextPage').disabled = page === totalPages;
                        }

                        displayResults(currentPage);

                        document.getElementById('prevPage').addEventListener('click', function() {
                            if (currentPage > 1) {
                                currentPage--;
                                displayResults(currentPage);
                            }
                        });

                        document.getElementById('nextPage').addEventListener('click', function() {
                            if (currentPage < totalPages) {
                                currentPage++;
                                displayResults(currentPage);
                            }
                        });
                    }
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('搜索时出错');
            });
        });

        function generateCode(type, subType, properties, placeholders, recordNumber) {
            const typeCode = {
                '电阻': '37',
                '电容': '38',
                '电感': '39',
                '复合开关': '105',
                '采集器': '104',
                '壳表': '1',
                '智能电容器':'07','表箱':'08','连接器':'84',
                '继电器':'185','电流互感器':'186','电流传感器':'187','充电桩':'109','智能井盖':'188','故障指示器':'189','断路跳闸触发控制器':'190','其他成品':'199',
            };
            const subTypeCode = {
                '贴片电阻': '001','插装电阻': '012','压敏电阻': '004','热敏电阻': '003','电位器':'005',
                '贴片电容': '021','插件电容': '022',
                '贴片电感': '006','插件电感': '007',
                '单相壳表': '01','三相壳表': '02','负控壳表': '03',
            };
            const propertyCode = properties.map((property, index) => {
                if (property.trim() !== '') {
                    if (placeholders[index] === '备案号' || placeholders[index] === '申请人' || placeholders[index] === '申请时间') {
                        return `${placeholders[index]}:${property.replace(/-/g, '')}`;
                    } else {
                        return `${placeholders[index]}:${property}`;
                    }
                    
                }
                return null;
            }).filter(item => item !== null).join('-');
            // 生成完整的编码，但只显示前缀部分和备案号
            console.log("propertyCode",propertyCode);
            console.log("properties",properties);
            const codePrefix = `T${typeCode[type]}${subType ? subTypeCode[subType] : ''}${recordNumber}`;
            const fullCode = `${codePrefix}-${propertyCode}`;
            return { fullCode, codePrefix };
        }
    </script>
</body>
</html>
