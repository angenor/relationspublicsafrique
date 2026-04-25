<template>
    <div class="spinner"></div>

</template>

<script>

</script>

<style scoped>
.spinner {
    display: grid;
    place-items: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: conic-gradient(
        from 180deg at 50% 50%,
        rgba(82, 0, 255, 0) 0deg,
        #ffffff 360deg
    );
    animation: spin 2s infinite linear;
}
.spinner::before {
    content: "";
    border-radius: 50%;
    width: 80%;
    height: 80%;
    background-color: #755555;
}

@keyframes spin {
    to {
        transform: rotate(1turn);
    }
}

</style>
