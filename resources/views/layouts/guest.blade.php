<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manajemen Proyek</title>

    <script src="https://cdn.tailwindcss.com"></script>

<style>
    body {
        background: #f8f9ff;
        position: relative;
        overflow: hidden;
        min-height: 100vh;
        margin: 0;
    }

    /* Circle ungu */
    body::before {
        content: "";
        position: absolute;
        top: -120px;
        left: -120px;
        width: 620px;
        height: 520px;
        background: rgba(172, 123, 255, 1);
        border-radius: 50%;
        filter: blur(120px);
    }

    /* Circle biru */
    body::after {
        content: "";
        position: absolute;
        bottom: -120px;
        right: -120px;
        width: 620px;
        height: 520px;
        background: rgba(119, 182, 255, 1);
        border-radius: 50%;
        filter: blur(120px);
    }

    .text-purple {
    font-size: 13px; 
    font-weight: 500;   
    color: #ac7bffff;      
    }

    .column-input {
    margin-top: 2px;        
    display: block;            
    width: 100%;                
    border-radius: 25px;     
    border: 1px solid #d1d5db;  
    box-shadow: 0 1px 2px rgba(0,0,0,0.05); 
    font-size: 0.875rem;        
    padding: 0.5rem 0.75rem;    
    }

    .column-input:focus {
    border-color: #ac7bffff;
    outline: none;
    box-shadow: 0 0 0 1px #ac7bffff;
    }

    .btn-submit {
    width: 100%;
    display: flex;
    justify-content: center;
    padding: 0.5rem 1rem; 
    border-radius: 25px; 
    background-color: #77b6ffff; 
    color: white;
    font-weight: 600; 
    box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
    }

    .btn-submit:hover {
    background-color: #469cffff; 
    }

    .btn-submit:focus {
    outline: none; 
    box-shadow:
        0 0 0 2px white,        
        0 0 0 4px #6366F1;      
    }
    
    .card-container {
        width: 100%;
        max-width: 24rem;
        display: flex;
        flex-direction: column;
        background-color: rgba(255, 255, 255, 0.3);
        padding: 1.5rem; /* kurang dari 2rem */
        border-radius: 1rem;
        box-shadow: 0 25px 50px rgba(0,0,0,0.25);
        position: relative;
        z-index: 10;
    }

    .card-container h2 {
        margin-top: 0; /* hilangkan mt-2 */
    }
</style>

</head>

<body class="flex items-center justify-center py-12">
    {{ $slot }}
</body>
</html>
