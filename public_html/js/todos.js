(function() {
  'use strict';

  var vm = new Vue({
    el: '#app',
    data: {
      newItem: '',
      todos: []
    },
    watch: {
      todos: {
        handler: function() {
          localStorage.setItem('todos', JSON.stringify(this.todos));
          // alert('Data saved');
        },
        deep: true
      }
    },
    mounted: function() {
      this.todos = JSON.parse(localStorage.getItem('todos')) || [];
    },
    methods: {
      addItem: function() {
        var item = {
          title: this.newItem,
          isDone: false
        };
        this.todos.push(item);
        this.newItem = '';
      },
      deleteItem: function(index) {
        if (confirm('本当に削除しますか？')) {
          // splice => index番目から何個削除
          this.todos.splice(index, 1);
        }
      },
      purge: function() {
        if (!confirm('終了済みタスクを削除しますか？')) {
          // cancel
          return;
        }
        this.todos = this.remaining;

      }
    },
    computed: {
      remaining: function() {
        return this.todos.filter(function(todo){
          return !todo.isDone;
        });
      }
    }

  });
})();
