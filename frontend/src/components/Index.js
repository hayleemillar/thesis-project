import React, { Component } from "react";
import { render } from "react-dom";

class Index extends Component {
  constructor(props) {
    super(props);
    this.state = {};
  }

  handleSubmit(event) {
  }

  render() {
    console.log('Index');
    return (
        <p class="index-test">this is a react component test</p>
    );
  }
}

export default Index;

const container = document.getElementById("index-app");
render(<Index />, container);