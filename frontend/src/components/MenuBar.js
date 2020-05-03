import React, { Component } from "react";
import { render } from "react-dom";

class MenuBar extends Component {
  constructor(props) {
    super(props);
    this.state = {};
  }

  handleSubmit(event) {
  }

  render() {
    console.log("MenuBar");
    return (
      <p>"hello MenuBar"</p>
    );
  }
}

export default MenuBar;

const container = document.getElementById("menu-app");
render(<Index />, container);