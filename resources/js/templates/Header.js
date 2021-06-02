import React from "react"; 
import logo from '../../../public/images/logo.png'

class Header extends React.Component {

    logout() {

        axios.post('/logout')
            .then( res => {
                window.location.href = '/login'
            })
    }

    render() {

        return ( 
            <header className="topbar" data-navbarbg="skin5">
                <nav className="navbar top-navbar navbar-expand-md navbar-dark">
                    <div className="navbar-header" data-logobg="skin5"> 
                        <a className="navbar-brand" href="index.html">
                        
                            <b className="logo-text" style={{marginLeft:'10px'}}> 
                                <img src={logo} width={34} /> Hero Journals
                            </b>
                        </a>  
                    </div> 
                    
                    <div style={{width:"100%",paddingRight:'20px'}}>
                        <ul className="navbar-nav float-end"> 
                            <li className="nav-item dropdown"> 
                                <div className="dropdown ">
                                    <a className="nav-link dropdown-toggle text-muted waves-effect waves-dark pro-pic" id="navbarDropdown" data-toggle="dropdown">
                                        Alger Makiputin <i className="mdi mdi-chevron-down"></i>
                                    </a>
                                    <div className="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <a className="dropdown-item" href="javascript:void(0)" onClick={() => this.logout()}>Logout</a> 
                                    </div>
                                </div>
                            </li> 
                        </ul>
                    </div>
                </nav>
            </header>
        )
    }
} 

export default Header