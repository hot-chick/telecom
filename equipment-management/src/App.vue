<template>
  <div id="app">
    <nav>
      <router-link style="margin-right: 10px;" to="/">Список оборудования</router-link>
      <router-link to="/equipment/new">Добавить оборудование</router-link>
      
    </nav>
    <router-view />
  </div>

</template>


<script>


</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}

.eq{
  margin: 0 auto;
}

li {
  list-style-type: none;
}

body {
  width: 80%;
  margin: 0 auto;
}

a {
  color: rgb(0, 0, 0);
  font-size: 18px;
  text-decoration: none;
}

h2 {
  font-size: 1.5em;
  color: #333;
  margin-bottom: 20px;
}

ul {
  list-style-type: none;
  padding: 0;
}

li {
  border: 1px solid #ddd;
  margin: 10px 0;
  padding: 15px;
  border-radius: 5px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

li span {
  font-size: 0.9em;
  color: #666;
}

button {
  padding: 8px 12px;
  background-color: #007BFF;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 1em;
  transition: background-color 0.3s ease;
}

button:hover {
  background-color: #0056b3;
}

button:focus {
  outline: none;
}

form {
  display: flex;
  flex-direction: column;
  gap: 15px;
  max-width: 500px;
  margin: 0 auto;
  padding: 20px;
  background-color: #f9f9f9;
  border-radius: 5px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

label {
  font-size: 1.1em;
  color: #333;
}

select,
input,
textarea {
  padding: 10px;
  font-size: 1em;
  border: 1px solid #ddd;
  border-radius: 5px;
}

select:focus,
input:focus,
textarea:focus {
  border-color: #007BFF;
  outline: none;
}

button[type="submit"] {
  
  background-color: #28a745;
  font-weight: bold;

}
button{
  margin-right: 10px;
}

button[type="submit"]:hover {
  background-color: #218838;
}

button[type="submit"]:focus {
  outline: none;
}

input[type="text"],
textarea {
  width: 100%;
}

input[type="text"]:focus,
textarea:focus {
  border-color: #007BFF;
}

input[type="text"] {
  padding: 8px;
  margin-bottom: 10px;
}

.loading {
  font-size: 1.2em;
  color: #007BFF;
  text-align: center;
  margin-top: 20px;
}
</style>
