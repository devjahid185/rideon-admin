<!DOCTYPE html>
<html>
<head>
<style>
  body {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
    margin: 0;
    background-color: #F8FAF5;
  }
  .loader {
    border: 8px solid #4CAF50;
    border-top: 8px solid #ffffff;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
  }
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
</style>
</head>
<body>
  <div class="loader"></div>
</body>
</html>
