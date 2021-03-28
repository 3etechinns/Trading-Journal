import React, { useContext } from "react"
import TradeForm from '../forms/TradeForm'
import Trades from '../../model/Trades' 
import SellForm from '../forms/SellForm'
import EquityChart from '../charts/EquityChart'
import AccountSummary from '../cards/AccountSummary' 

var positions = [];

class Dashboard extends React.Component {

    constructor(props) {
        super(props)

        this.state = {
            positions: [],
            showSellModal: false,
            toSell: []
        }
        
        this.open_positions = this.open_positions.bind(this);
        this.handleSellModal = this.handleSellModal.bind(this);
        this.closeSellModal = this.closeSellModal.bind(this);
        this.open_positions()
        
    } 

    async open_positions() {
        

        await Trades.position()
            .then( res => {
  
               this.setState({positions: res}) 

            })
      
    }

    handleSellModal(trade) {

        this.setState({toSell: trade})
        this.setState({showSellModal: !this.state.showSellModal})
    }

    closeSellModal() {

        this.setState({showSellModal: !this.state.showSellModal})
    }

    portfolioList() {
 
        const openTrades = this.state.positions;
        let positionsList = <tr>
            <td colSpan="5">No data available</td>
        </tr>

        if ( Object.keys(this.state.positions).length ) {

            positionsList = openTrades.map( (trade, index) => {
                return <tr key={trade.stock_code + index }>
                    <td> {trade.stock_code }</td>
                    <td> {trade.ave_price }</td>
                    <td> {trade.total_shares }</td>
                    <td> {trade.total_cost }</td>
                    <td> 
                        <div className="dropdown show">
                        <i className="mdi mdi-wrench dropdown-toggle" href="#" role="button" id="portfolioActions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        </i> 
                            <div className="dropdown-menu" aria-labelledby="portfolioActions">
                                <a onClick={ () => this.handleSellModal(trade) } className="dropdown-item" href="#">Sell</a> 
                            </div>
                        </div>    
                    </td>
                </tr>
            })
        } 
 
        return ( 
            <tbody>{ positionsList }</tbody>
        )
 
    }
 
    render() {       

        return (
            <div className="page-wrapper">  
             
                <div className="page-breadcrumb">
                    <div className="row align-items-center">
                        <div className="col-5">
                            <h4 className="page-title">Dashboard</h4>
                            <div className="d-flex align-items-center">
                                <nav aria-label="breadcrumb">
                                    <ol className="breadcrumb">
                                        <li className="breadcrumb-item"><a href="#">Home</a></li>
                                        <li className="breadcrumb-item active" aria-current="page">Dashboard</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                        <div className="col-7">
                            <div className="text-end upgrade-btn"> 
                                <TradeForm 
                                    positions={ this.open_positions } 
                                    listPositions = { this.portfolioList }
                                /> 
                            </div>
                        </div>
                    </div>
                </div> 
                <div className="container-fluid"> 
                    <div className="row">
                        <div className="col-md-8">
                            <div className="card">
                                <div className="card-body">
                                    <div className="d-md-flex align-items-center">
                                        <div>
                                            <h4 className="card-title">Equity Curve</h4>
                                            <h5 className="card-subtitle">Overview of Latest 12 Months </h5>
                                        </div> 
                                    </div>
                                    <div className="row"> 
                                        <div className="col-lg-12"> 
                                            <EquityChart /> 
                                        </div> 
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="col-md-4">
                            <AccountSummary />
                        </div>
                    </div> 
                    <div className="row"> 
                        <div className="col-12">
                            <div className="card">
                                <div className="card-body"> 
                                    <div className="d-md-flex">
                                        <div>
                                            <h4 className="card-title">Stock Position</h4>
                                            <h5 className="card-subtitle">Overview of my open trades</h5>
                                        </div>
                                        <div className="ms-auto">
                                            <div className="dl">
                                                {/* <select className="form-select shadow-none">
                                                    <option value="0">Monthly</option>
                                                    <option value="1">Daily</option>
                                                    <option value="2">Weekly</option>
                                                    <option value="3">Yearly</option>
                                                </select> */}
                                            </div>
                                        </div>
                                    </div> 
                                </div>
                                <div className="table-responsive">
                                     
                                    <table className="table v-middle">
                                        <thead>
                                            <tr className="bg-light">
                                                <th className="border-top-0">Stock</th>
                                                <th className="border-top-0">Ave. Price</th>
                                                <th className="border-top-0">Shares</th>
                                                <th className="border-top-0">Total Cost</th> 
                                                <th width="100"></th>
                                            </tr>
                                        </thead> 
                                        { this.portfolioList() } 
                                    </table> 
                                    <SellForm 
                                        show={this.state.showSellModal }  
                                        handleModal= { this.handleSellModal }
                                        trade= {this.state.toSell}
                                        closeHandle = { this.closeSellModal }
                                        />
                                </div>
                            </div>
                        </div>
                    </div>  
                </div>
                <footer className="footer text-center">
                    Trading Journal PH
                </footer> 
            </div>    
        )
    }


}

export default Dashboard;
